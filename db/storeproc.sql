
/****** Object:  StoredProcedure [dbo].[sp_updateSalesTotal]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    procedure [dbo].[sp_updateSalesTotal]
	@salesid varchar(20)
as

update T1 set 
	totalorder = T2.totalorder, 
	discount = T2.discount, 
	totalbayar = T2.totalbayar + T1.paymentcharge
from salestable T1 inner join						   
(
	select salesid, sum(totalorder) as totalorder, sum(discount) as discount, sum(totalbayar) as totalbayar 
	from salesline with (nolock) where salesid = @salesid group by salesid 
) T2 on T1.salesid = T2.salesid
where T1.salesid = @salesid
GO
/****** Object:  StoredProcedure [dbo].[sp_salesByPassed]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE        procedure [dbo].[sp_salesByPassed]
as

SET NOCOUNT ON
declare 
	@timestamp 		bigint,
	@SalesId		varchar(20),
	@requestid		varchar(10),
	@purchPPN		decimal(18,3),
	@curdatetime	datetime


-- get parameters
select
	@purchPPN = bcincludeppn
from sysparamtable with (nolock)


set @requestid = 'order'
set @curdatetime = getdate()
set @timestamp = dbo.Date2UnixTimeStamp(@curdatetime)


if exists(
	select salesid from salestable 
	where status = 2 and -- salesOrdered
		datediff(mi, maxvalidatedate, @curdatetime) > 0)
begin

	--update salesstatus to bypassed
	update salestable set 
		status = 3, --salesByPassed
		bypasseddate = @curdatetime
	where 
		status = 2 and --salesOrdered  
		datediff(mi, maxvalidatedate, @curdatetime) > 0

	--update salesline qtybc = 0
	update T1 set qtybc = 0
	from salesline T1 
	inner join salesTable T2 with(nolock) on T1.salesid = T2.salesid
	where T2.status = 3 --salesByPassed

	--just make sure no purchtable for salestable with salesstatus bypassed
	delete T1 from purchtable T1 
	inner join SalesTable T2 with(nolock) on T1.purchid = T2.salesid
	where T2.status = 3 --salesByPassed

	--create purchline
	insert into purchline (purchid, itemid, qty, price, pricebc, rowid, totalorder, discount, totalbayar)
	select 
		T1.salesid, T1.itemid, T1.qty,
		T3.pricecatalog, T3.pricebc, T1.rowid,
		T3.pricecatalog * T1.qty,
		(T3.pricebc * T1.qty) - (T3.pricecatalog * T1.qty),
		T3.pricebc * T1.qty
	from salesline T1
	inner join salesTable T2 with(nolock) on T1.salesid = T2.salesid
	inner join inventTable T3 with(nolock) on T1.itemid = T3.itemid 
	where T2.status = 3 --salesByPassed

	--create purchtable
	insert into PurchTable 
		(purchid, kodebc, orderdate, bypasseddate, synctimestamp, status, 
		totalorder, discount, totalbayar, includeppn)
	select 
		T1.purchid, T2.KodeBC, @curdatetime, @curdatetime, @timestamp, 2, --purchOrdered 
		sum(T1.totalorder) as totalorder, sum(T1.discount) as discount, sum(T1.totalbayar) as totalbayar,
		(sum(T1.totalbayar) * @purchPPN / 100)
	from purchline T1 
	inner join salesTable T2 with(nolock) on T1.purchid = T2.salesid
	where T2.status = 3 --salesByPassed	
	group by T1.purchid, T2.KodeBC 
	
	-- create synctable
	insert into syncordertable 
	(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail) 
	select @timestamp, salesid, kodebc, kodemember, alamat, telp, email
	from salestable with(nolock) where status = 3 --salesByPassed
	
	insert into syncorderline
	(timestamp, sessionid, rowid, itemid, price, qtyorder)
	select @timestamp, T1.purchid, T1.rowid, T1.itemid, T1.price, T1.qty 
	from purchline T1 with(nolock)
	inner join salesTable T2 with(nolock) on T1.purchid = T2.salesid
	where T2.status = 3 --salesByPassed	
	
	insert into syncrequest (timestamp, sessionid, requestid, status)
	select @timestamp, salesid, @requestid, 0 
	from salestable with(nolock) where status = 3 --salesByPassed	

	-- update salestable status
	update salestable set
		status = 4 --salesInProgress
	where status = 3 --salesByPassed	

end 

SET NOCOUNT OFF
GO
/****** Object:  StoredProcedure [dbo].[sp_paygate_PaymSuccess]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when member confirm the qty change
CREATE  procedure [dbo].[sp_paygate_PaymSuccess]
	@salesid 	varchar(20),
	@reference	varchar(20),
	@trxref		varchar(20),
	@trxdate	varchar(20)
AS

DECLARE @confirmDate datetime
DECLARE @safetyDelay int
DECLARE @minPaidDate datetime

SET NOCOUNT ON

-- SELECT PAYMENT SUCCESS PARAMETERS
-- confirmDate:		date of confirmation (now)
-- safetyDelay: 	timeout for holding out the order after payment confirmation (or 0 if no hold)
-- minPaidDate:		min date for marking order as paid ( = confirmDate + safetyDelay )

SELECT 	@confirmDate = GETDATE(),	
	@minPaidDate = DATEADD(s, PM.safetydelay, GETDATE())
FROM SalesTable ST
	INNER JOIN PaymentTable PT
		ON PT.salesid = ST.salesid
	INNER JOIN PaymentMode PM
		ON PT.paymentmode = PM.paymentmode
WHERE ST.salesid = @salesid

IF @@ROWCOUNT = 0 RETURN 0

-- TRANSACTION TO CHANGE ORDER STATUS AND UPDATE PAYMENTTABLE
BEGIN TRANSACTION t1

	-- UPDATE salestable first, to avoid parallel cancellation due to timeout
	UPDATE SalesTable SET 
		status = 7,
		confirmeddate = @confirmDate,
		maxpaiddate = NULL
	FROM SalesTable ST
	INNER JOIN PaymentTable PT ON PT.salesid = ST.salesid
	WHERE ST.salesid = @salesid 
		-- SANITY CHECK: status is validated
		AND ST.status = 6
		-- SANITY CHECK: payment status is initialized
		AND PT.paymstatus = 1
		-- SANITY CHECK: Confirmation trxref must be = to initialization trxref for security
		AND PT.trxref = ISNULL(@trxref, PT.trxref)
	
	IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
		BEGIN
		ROLLBACK TRANSACTION t1
		RETURN 0
		END

	-- UPDATE paymenttable
	UPDATE PaymentTable SET
		paymstatus = 2,
		reference = @reference,
		confirmeddate = @confirmDate,
		minpaiddate = @minPaidDate,
		trxdate = @trxdate,
		maxpaiddate = NULL
	FROM PaymentTable where salesid = @salesid 

IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
	BEGIN
	ROLLBACK TRANSACTION t1
	RETURN 0
	END
ELSE
	COMMIT TRANSACTION t1

SET NOCOUNT OFF
RETURN @@ROWCOUNT
GO
/****** Object:  StoredProcedure [dbo].[sp_paygate_PaymReconcile]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when accounting has successfully reconciled the payment
CREATE   procedure [dbo].[sp_paygate_PaymReconcile]
	@salesid 	varchar(20),
	@reference	varchar(20) = NULL
AS

DECLARE @reconcileDate datetime

SET NOCOUNT ON

-- SELECT PAYMENT RECONCILIATION PARAMETERS
-- reconcileDate:	date of reconciliation (now)

SET @reconcileDate = GETDATE()

-- TRANSACTION TO CHANGE ORDER STATUS AND UPDATE PAYMENTTABLE
BEGIN TRANSACTION t1

	-- UPDATE salestable first, to avoid parallel cancellation due to timeout
	UPDATE SalesTable SET 
		status = 8,
		paiddate = @reconcileDate,
		maxpaiddate = NULL
	FROM SalesTable ST
	INNER JOIN PaymentTable PT ON PT.salesid = ST.salesid
	WHERE ST.salesid = @salesid 
		-- SANITY CHECK: status is validated or confirmed
		AND ST.status >= 6
		AND ST.status < 8
		-- SANITY CHECK: payment status is not yet reconciled
		AND PT.paymstatus < 4
	
	IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
		BEGIN
		ROLLBACK TRANSACTION t1
		RETURN 0
		END

	-- UPDATE paymenttable
	UPDATE PaymentTable SET
		paymstatus = 4,
		reference = ISNULL(@reference,reference),
		reconcileddate = @reconcileDate,
		maxpaiddate = NULL
	FROM PaymentTable where salesid = @salesid 

IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
	BEGIN
	ROLLBACK TRANSACTION t1
	RETURN 0
	END
ELSE
	COMMIT TRANSACTION t1

SET NOCOUNT OFF
RETURN @@ROWCOUNT
GO
/****** Object:  StoredProcedure [dbo].[sp_paygate_PaymInit]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when payment is initiated
CREATE    procedure [dbo].[sp_paygate_PaymInit]
	@salesid 	varchar(20),
	@trxref		varchar(20),
	@trxdate	varchar(20)
as

DECLARE @initializedDate datetime
DECLARE @maxInitiateDate datetime
DECLARE @maxReconcileDate datetime
DECLARE @maxPaymDate datetime
DECLARE @maxPaidDate datetime
DECLARE @minPaidDate datetime

SET NOCOUNT ON

-- SELECT PAYMENT INITIALIZATION PARAMETERS
-- initializedDate:	date of initialization (now)
-- maxInitiateDate: 	max date for holding out the order before payment confirmation ( = initializedDate + initTimeOut )
-- maxReconcileDate:	max date for holding out the order before reconciliation ( = initializedDate + reconcileTimeOut )
-- maxPaymDate:		max date for proceeding to payment ( = validateDate + paymtimeout)
-- maxPaidDate:		max date for payment before order cancellation
-- minPaidDate:		min date to wait before order reconciliation

SELECT 	@initializedDate = GETDATE(),	
	@maxInitiateDate = DATEADD(s, PM.initiatetimeout, GETDATE()),
	@maxPaymDate = PT.maxpaymdate,
	@maxReconcileDate = DATEADD(s, PM.reconciletimeout, GETDATE())
FROM SalesTable ST
	INNER JOIN PaymentTable PT
		ON PT.salesid = ST.salesid
	INNER JOIN PaymentMode PM
		ON PT.paymentmode = PM.paymentmode
WHERE ST.salesid = @salesid

IF @maxPaymDate IS NULL RETURN 0

SET @minPaidDate = CASE WHEN @maxInitiateDate>@maxPaymDate THEN @maxInitiateDate ELSE @maxPaymDate END
SET @maxPaidDate = CASE WHEN @maxReconcileDate>@maxPaymDate THEN @maxReconcileDate ELSE @maxPaymDate END

BEGIN TRANSACTION t1

	-- UPDATE salestable first, to avoid parallel cancellation due to timeout
	UPDATE SalesTable SET maxpaiddate = @maxPaidDate
	FROM SalesTable ST
	INNER JOIN PaymentTable PT ON PT.salesid = ST.salesid
	WHERE ST.salesid = @salesid 
		-- SANITY CHECK: Order status = validate
		AND ST.status = 6
		-- SANITY CHECK: Payment status <> successful, reconciled
		AND PT.paymstatus <> 2 AND PT.paymstatus <> 4
		-- SANITYCHECK: Initialization is done before end of payment grace period
		AND @initializedDate <= @maxPaymDate

	IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
		BEGIN
		ROLLBACK TRANSACTION t1
		RETURN 0
		END

	-- UPDATE paymenttable data
	UPDATE PaymentTable SET
		trxref = @trxref,
		trxdate = @trxdate,
		paymstatus = 1, -- INITIALIZED
		initializedate = @initializedDate,
		maxPaidDate = @maxPaidDate,
		minPaidDate = @minPaidDate
	FROM PaymentTable WHERE salesid = @salesid 

IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
	BEGIN
	ROLLBACK TRANSACTION t1
	RETURN 0
	END
ELSE
	COMMIT TRANSACTION t1

SET NOCOUNT OFF
RETURN @@ROWCOUNT
GO
/****** Object:  StoredProcedure [dbo].[sp_paygate_PaymFail]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when member confirm the qty change
CREATE              procedure [dbo].[sp_paygate_PaymFail]
	@salesid 	varchar(20),
	@trxref		varchar(20)= NULL
	
AS

SET NOCOUNT ON

-- TRANSACTION TO CHANGE ORDER STATUS AND UPDATE PAYMENTTABLE
BEGIN TRANSACTION t1

	-- UPDATE salestable first, to avoid parallel cancellation due to timeout
	UPDATE SalesTable SET 
		status = 6,
		maxpaiddate = PT.maxpaymdate,
		confirmeddate = NULL
	FROM SalesTable ST
	INNER JOIN PaymentTable PT ON PT.salesid = ST.salesid
	WHERE ST.salesid = @salesid 
		-- SANITY CHECK: order status is validated, confirmed
		AND (ST.status = 6 OR ST.status = 7) 
		-- SANITY CHECK: status not yet confirmed
		AND PT.paymstatus < 4
		-- SANITY CHECK: trxref is specified and same as during initialization
		AND PT.trxref = ISNULL(@trxref, PT.trxref)
	
	IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
		BEGIN
		ROLLBACK TRANSACTION t1
		RETURN 0
		END

	-- UPDATE paymenttable
	UPDATE PaymentTable SET
		paymstatus = 3,
		initializedate = NULL,
		confirmeddate = NULL,
		maxpaiddate = maxpaymdate,
		minpaiddate = NULL,
		reference = NULL
	FROM PaymentTable where salesid = @salesid 

IF @@ROWCOUNT = 0 OR @@ERROR <> 0 
	BEGIN
	ROLLBACK TRANSACTION t1
	RETURN 0
	END
ELSE
	COMMIT TRANSACTION t1

SET NOCOUNT OFF
RETURN @@ROWCOUNT
GO
/****** Object:  StoredProcedure [dbo].[sp_paygate_PaymCreate]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when member confirm the qty change
CREATE     procedure [dbo].[sp_paygate_PaymCreate]
	@salesid 	varchar(20),
	@paymentmode	varchar(20),
	@virtualaccount varchar(20) = NULL,
	@msisdn		varchar(20) = NULL,
	@createdate	datetime = NULL
as

DECLARE @startDate datetime
DECLARE @maxpaymdate datetime

SET NOCOUNT ON

SELECT 	@startDate = ISNULL(@createdate,GETDATE()),	
	@maxpaymdate = DATEADD(s, PM.paymtimeout, @startDate)
FROM PaymentMode PM
WHERE PM.paymentmode = @paymentmode

-- INSERT in payment table
INSERT INTO PaymentTable (
	salesid,
	paymentmode, 
	virtualaccount, 
	msisdn,
	startdate,
	maxpaymdate,
	maxpaiddate
) VALUES (
	@salesid,
	@paymentmode, 
	@virtualaccount, 
	@msisdn,
	@startdate,
	@maxpaymdate,
	@maxpaymdate
)

-- UPDATE salestable
UPDATE SalesTable SET maxpaiddate = @maxpaymdate
FROM SalesTable WHERE salesid = @salesid 

SET NOCOUNT OFF

RETURN @@ROWCOUNT
GO
/****** Object:  StoredProcedure [dbo].[sp_getNextNo]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   procedure [dbo].[sp_getNextNo]
	@Category varchar(20),
	@breakby varchar(5) = 'YEAR'
as

set nocount on
Declare 
	@nextno  int,
	@newno  varchar(12)

if upper(@breakby) = 'YEAR' or upper(@breakby) = 'YR' 
begin
	update sequenceTable
	set 
		@nextno = case when Tahun = year(getdate()) then nextno else 1 end,
		nextno  = case when Tahun = year(getdate()) then nextno + 1 else 2 end,
		Tahun = year(getdate())
	where Category = @Category 
	
	set @newno = right(cast(year(getdate()) as varchar(4)),2) + 
					right('0'+cast(month(getdate()) as varchar(4)),2) +
				    right('00000000' + cast(@nextno as varchar(8)),8)
end 

if upper(@breakby) = 'MONTH' or upper(@breakby) = 'MTH' 
begin
	update sequenceTable
	set 
		@nextno = case when Tahun = year(getdate()) and Bulan = month(getdate())
						then nextno else 1 end,
		nextno  = case when Tahun = year(getdate()) and Bulan = month(getdate())
						then nextno + 1 else 2 end,
		Tahun = year(getdate()),
		Bulan = month(getdate())
	where Category = @Category
	
	set @newno = right(cast(year(getdate()) as varchar(4)),2) + 
					right('0'+cast(month(getdate()) as varchar(4)),2) +
				    right('00000000' + cast(@nextno as varchar(8)),8)
end 

select @newno as newno

set nocount off
GO
/****** Object:  StoredProcedure [dbo].[sp_createSync]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE          procedure [dbo].[sp_createSync]
--declare	
	@salesid		varchar(20),
	@requestid		varchar(10)
as

declare 
	@timestamp 	bigint,
	@nostock	int

-- make sure only purchline with qty > 0 are sync
delete purchline where purchid = @salesid and qty <= 0

if exists(select purchid from purchline with(nolock) where purchid = @salesid and qty > 0)
begin
	set @timestamp = dbo.Date2UnixTimeStamp(getdate())
	
	insert into syncordertable 
	(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail) 
	select @timestamp, @salesid, kodebc, kodemember, alamat, telp, email
	from salestable with(nolock) where salesid = @salesid
	
	insert into syncorderline
	(timestamp, sessionid, rowid, itemid, price, qtyorder)
	select @timestamp, @salesid, rowid, itemid, pricebc, qty from purchline with(nolock)
	where purchid = @salesid and qty > 0
	
	insert into syncrequest (timestamp, sessionid, requestid, status) values
	(@timestamp, @salesid, @requestid, 0)
	

end

if @requestid = 'order'
begin
	update purchtable set 
		synctimestamp = @timestamp,
		status = 2, --purchOrdered,
		responsecode = null --kalo null berarti belum dapat feedback dari sync proses
	where purchid = @salesid

	update salestable set
		status = 4 --salesInProgress
	where salesid = @salesid
end
else --cancelorder
begin
	-- untuk cancel order perlu di trace lagi?
	-- bagaimana kalo sync cancel order gagal ?
	update purchtable set 
		synctimestamp = @timestamp,
		status = 0, --purchCancelled
		responsecode = null --kalo null berarti belum dapat feedback dari sync proses
	where purchid = @salesid

	-- cancel order
	update salestable set
		status = 0 --salesCancelled
	where salesid = @salesid		
end
GO
/****** Object:  StoredProcedure [dbo].[sp_checkQuantity]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		Ivan Widjaksono
-- Create date: 
-- Description:	
-- =============================================
CREATE PROCEDURE [dbo].[sp_checkQuantity] 
	-- Add the parameters for the stored procedure here
	@itemid varchar(30)
AS
BEGIN

Declare @qtyAvailable bigint,
		@qtyNotYetImport bigint;

	select top 1 @qtyAvailable = isnull(qty,0) from inventTable with (NOLOCK)
		where itemid = @itemid
	
	select top 1 @qtyNotYetImport = isnull(sum(qty),0) from SalesLine as T1 with (NOLOCK)
		where	itemid = @itemid
		and		exists( select top 1 salesid from salesTable with (NOLOCK)
							where	salesid	=	T1.salesid
							and		status	in	(2,3,4) )
							
	select 	@qtyAvailable-@qtyNotYetImport;
	

END
GO
/****** Object:  StoredProcedure [dbo].[sp_updateSalesLine]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE     procedure [dbo].[sp_updateSalesLine]
	@salesid 	varchar(20),
	@itemid		varchar(10),
	@qty		decimal(18,0),
	@addQty		smallint = 1
as

declare @rowid smallint

if @addQty = 1
begin
	if @qty > 0
	begin
		if exists(select itemid from salesline with (nolock) where salesid = @salesid and itemid = @itemid)
		begin
			update salesline set
				qty = qty + @qty,
				qtyorder = qtyorder + @qty, 
				totalorder = price * (qty + @qty),
				discount = (pricembr * (qty + @qty)) - 	(price * (qty + @qty)),
				totalbayar = pricembr * (qty + @qty) 
			where salesid = @salesid and itemid = @itemid		
		end
		else
		begin

			select @rowid = isnull(max(rowid),0) + 1 from salesline with (nolock) where salesid = @salesid

			insert into salesline (salesid, itemid, qty, qtyorder, price, pricembr, totalorder, discount, totalbayar, rowid)
			select @salesid, upper(@itemid), @qty, @qty, pricecatalog, pricembr, pricecatalog * @qty,
	 			   (pricembr * @qty) - 	(pricecatalog * @qty), pricembr * @qty, @rowid
			from InventTable with (nolock) where itemid = @itemid
			--note: inventtable use with(nolock) assume the price is not change every time and
            --      avoid locking when SMI update the qty
		end
	end 
end
else
begin
	if @qty > 0
	begin
		update salesline set
			qty = @qty,
			qtyorder = @qty,
			totalorder = price * @qty,
			discount = (pricembr * @qty) - 	(price * @qty),
			totalbayar = pricembr * @qty 
		where salesid = @salesid and itemid = @itemid
	end
	else
	begin
		delete salesline where salesid = @salesid and itemid = @itemid
	end
end
GO
/****** Object:  StoredProcedure [dbo].[sp_updatepurchTotal]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE       procedure [dbo].[sp_updatepurchTotal]
	@purchid varchar(20)
as

declare 
	@purchPPN decimal(18,3)

select @purchPPN = bcincludeppn from sysparamtable with (nolock)

update T1 set 
	totalorder = T2.totalorder, 
	discount = T2.discount, 
	totalbayar = T2.totalbayar,
	includeppn = T2.totalbayar * @purchPPN / 100
from purchtable T1 inner join						   
(
	select purchid, sum(totalorder) as totalorder, sum(discount) as discount, sum(totalbayar) as totalbayar 
	from purchline where purchid = @purchid group by purchid 
) T2 on T1.purchid = T2.purchid
where T1.purchid = @purchid
GO
/****** Object:  StoredProcedure [dbo].[sp_updatePaymentMode]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   procedure [dbo].[sp_updatePaymentMode]
	@salesid 	varchar(20),
	@paymentmode varchar(10) = '',
	@mobilenumber varchar(30) = ''
as

declare 
	@chargeratio		as decimal,
	@chargefee 			as decimal,
	@virtualaccount		as varchar(20)

set @virtualaccount = ''
if @paymentmode = 'ATM' 
begin
	set @virtualaccount = '130000' + @salesid			
end 

select 
	@chargeratio = chargeratio,
	@chargefee = chargefee
from paymentMode with(nolock)
where paymentmode = @paymentmode
and active = 1

update salestable set
	paymentmode = case @paymentmode when '' then paymentmode else @paymentmode end,
	virtualaccount = case when @paymentmode = 'ATM' then @virtualaccount else '' end,
	paymentcharge = (totalbayar * @chargeratio) / (100 + @chargeratio) + @chargefee,
	totalbayar = totalorder + discount + (totalbayar * @chargeratio / 100) + @chargefee,
	paymentMobileNumber = @mobilenumber
where salesid = @salesid
GO
/****** Object:  StoredProcedure [dbo].[sp_sos_INVENTTABLESYNC]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO
CREATE    PROCEDURE [dbo].[sp_sos_INVENTTABLESYNC]
AS

SET NOCOUNT ON

-- DELETE OLD PRODUCTS
DELETE FROM INVENTTABLE 
WHERE NOT EXISTS
(
	SELECT 'x' FROM inventTableMaster WITH (NOLOCK)
	WHERE RTRIM(ITEMID) = INVENTTABLE.ITEMID AND
	BLOCKED_ID = 0 AND
	CATALOG_ID = 'current'
)

-- UPDATE EXISTING ITEM IN INVENTTABLE
UPDATE INVENTTABLE
SET 
	itemname = ITM.ITEMNAME,
	qty = ISNULL(InventSum.qtyOnHandSos,0),
	priceCatalog = ITM.PriceCTL_ID,
	PriceMBR = ITM.PriceMBR_ID,
	PriceBC = ITM.PriceBC_ID,
	updateDate = GETDATE()
FROM 	INVENTTABLE IT
	INNER JOIN inventTableMaster ITM WITH (NOLOCK)
		ON ITM.ITEMID = IT.ITEMID
	LEFT OUTER JOIN inventSum WITH (NOLOCK)
		ON inventSum.ItemId = ITM.ItemId
WHERE	ITM.BLOCKED_ID = 0 AND
	ITM.CATALOG_ID = 'current'


-- INSERT NEW PRODUCTS
INSERT INTO INVENTTABLE (ITEMID, itemname, qty, PriceCatalog, PriceMBR, PriceBC, deadstyle, INVENTGROUPID, updateDate, createDate)
(
	SELECT 
		ITM.ITEMID,
		ITM.ITEMNAME,
		ISNULL(InventSum.qtyOnHandSos,0),
		ITM.PriceCTL_ID,
		ITM.PriceMBR_ID,
		ITM.PriceBC_ID,
		0,
		'DEFAULT',
		GETDATE(),
		GETDATE()
	FROM inventTableMaster ITM WITH (NOLOCK)
	LEFT OUTER JOIN inventSum WITH (NOLOCK)
		ON inventSum.ItemId = ITM.ItemId
	WHERE 	ITM.BLOCKED_ID = 0 AND
		ITM.CATALOG_ID = 'current' AND
		NOT EXISTS (
			SELECT 'x' FROM INVENTTABLE 
			WHERE ITEMID = ITM.ITEMID
		)    
)

SET NOCOUNT OFF
GO
/****** Object:  StoredProcedure [dbo].[sp_sos_IMPORTMEMBER]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    PROCEDURE [dbo].[sp_sos_IMPORTMEMBER]
	@memberid 	varchar(20)
AS

SET NOCOUNT ON

-- INSERT IF NOT EXISTING

INSERT INTO memberTable
SELECT @memberid, GETDATE(), 0, NAME, ADDRESS, PHONE, EMAIL
FROM SMS.dbo.CUSTTABLE
WHERE ACCOUNTNUM = @memberid 
AND NOT EXISTS (SELECT 'x' FROM memberTable WHERE kodemember = @memberid)

-- UPDATE ACCEPTDATE
UPDATE memberTable SET acceptdate = GETDATE() WHERE kodemember = @memberid

-- UPDATE THE BC MAPPING
insert into mappingtable
select MEMBERID, BCCODE, 0
FROM SMS.dbo.SMI_F1MEMBERDETAIL
WHERE MEMBERID = @MEMBERID
AND NOT EXISTS (SELECT TOP 1 'x' FROM mappingtable WITH (NOLOCK)
	WHERE kodemember = @MEMBERID AND kodebc = BCCODE) 
GROUP BY MEMBERID, BCCODE

SET NOCOUNT OFF
GO

/****** Object:  StoredProcedure [dbo].[sp_sos_UPDATEMEMBERMAPPING]    Script Date: 06/13/2013 17:37:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		Victor Moreau
-- ALTER  date: 2013/04/12
-- Description:	Updates the mapping BC/Member from data F1
-- =============================================
CREATE PROCEDURE [dbo].[sp_sos_UPDATEMEMBERMAPPING] 
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    ---------------------------------------
	-- Update F1 date on existing records
	---------------------------------------
	UPDATE MAPPINGTABLE
		SET LastF1Date = F1GROUP.LastF1Date
	--SELECT MAP.kodemember, MAP.kodebc, MAP.LastF1Date, F1GROUP.LastF1Date
	FROM MAPPINGTABLE MAP
		JOIN (
			SELECT F1.[MEMBERID],F1.[BCCODE],MAX(F1.[F1SMIDATE]) AS LastF1Date
			FROM [SMS].[dbo].[SMI_F1MEMBERDETAIL] F1
			GROUP BY F1.MEMBERID, F1.BCCODE
		) F1GROUP
		ON MAP.kodemember = F1GROUP.MemberId AND MAP.kodebc = F1GROUP.bccode
	WHERE MAP.LastF1Date <> F1GROUP.LastF1Date; 

	---------------------------------------
	-- Insert new records from F1
	---------------------------------------
	INSERT INTO MAPPINGTABLE
	SELECT F1.[MEMBERID],F1.[BCCODE],0,MAX(F1.[F1SMIDATE]) AS LastF1Date
	  FROM [SMS].[dbo].[SMI_F1MEMBERDETAIL] F1
	WHERE NOT EXISTS (
		SELECT 'x' FROM MAPPINGTABLE MAP
		WHERE MAP.kodemember = F1.MemberId AND MAP.kodebc = F1.bccode
	)
	GROUP BY F1.MEMBERID, F1.BCCODE;

	--------------------------------------------
	-- Delete records with last date < 6 months
	--------------------------------------------
	DELETE FROM MAPPINGTABLE 
	WHERE LastF1Date < DATEADD(month, -6, GETDATE());

	SET NOCOUNT OFF;

END



/****** Object:  StoredProcedure [dbo].[sp_AXProcess]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE  procedure [dbo].[sp_AXProcess]
	@timestamp	bigint,
	@sessionid	varchar(50),
	@status smallint,
	@partialqty smallint
as
declare 
	@RequestID 	varchar(10),
	@salesid	varchar(20),
	@batchid	varchar(5)

-- 
-- declare mycursor cursor READ_ONLY for
-- select RequestID, Timestamp, sessionid from syncRequest where status = 0
-- 
-- open mycursor
-- fetch next from mycursor into @RequestID, @timestamp, @sessionid
-- while @@FETCH_STATUS = 0
-- begin


	set @salesid = 
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer)) +
			char(cast((57 - 48 )*rand() + 48 as integer))

	set @batchid = 	
			char(cast((90 - 65 )*rand() + 65 as integer)) +
			char(cast((90 - 65 )*rand() + 65 as integer)) +
			char(cast((90 - 65 )*rand() + 65 as integer)) +
			char(cast((90 - 65 )*rand() + 65 as integer)) +
			char(cast((90 - 65 )*rand() + 65 as integer))

	update syncOrderLine set
		price = 99999,
		responsecode = '1',
		qtyimport = case when qtyorder - @partialqty > 0 then qtyorder - @partialqty else 0 end
	where timestamp = @timestamp and sessionid = @sessionid
		
	update syncOrderTable set
		salesid = @salesid,
		responsecode = '1',
		total = 99999
	where timestamp = @timestamp and sessionid = @sessionid

	update syncRequest set
		status = @status, 
		responsecode = '1',
		axprocess = @batchid,
		startdate = getdate(),
		enddate = getdate()
	where timestamp = @timestamp and sessionid = @sessionid

-- 	fetch next from mycursor into @RequestID, @timestamp, @sessionid
-- end 
-- 
-- close mycursor
-- deallocate mycursor
GO
/****** Object:  StoredProcedure [dbo].[sp_sendEmailAndSMS]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		Ivan Widjaksono
-- Create date: 
-- Description:	
-- =============================================
CREATE PROCEDURE [dbo].[sp_sendEmailAndSMS] 
	-- Add the parameters for the stored procedure here
	@salesid varchar(50), 
	@sendSMStoo smallint = 0,
	@memberEmailTemplate varchar(50) = '',
	@BCEmailTemplate varchar(50) = '',
	@SMSTemplate varchar(50) = ''
AS
BEGIN

	Declare 	@mbrcode  		varchar(10),
				@mbrname		varchar(50),
				@mbremail		varchar(80),
				@mbrphone		varchar(50),
				@bccode  		varchar(10),
				@bcname			varchar(50),
				@bcemail		varchar(80),

				@emailfrom		varchar(80),
				@emailsubject	varchar(80),
				@emailbody		varchar(500),
				@BCemailsubject	varchar(80),
				@BCemailbody	varchar(500),

				@smsbody		varchar(500),
				
				@currentdt		datetime


	select 
		@mbrcode = kodemember,  		
		@mbrname = namamember,
		@mbremail = email,
		@mbrphone = telp,
		@bccode	 = kodebc,
		@bcname  = namabc,
		@bcemail = emailbc
	from vw_salestable with(nolock)
	where salesid = @salesid
	
	select top 1 
		@emailfrom = emailfrom
	from sysparamtable with (nolock)
	
	set @currentdt = GETDATE()
	
	-- Send email to BC --	
	if ( ISNULL(@BCEmailTemplate, '') != '' AND ISNULL(@bcemail,'') != '' )
	Begin
		select @emailsubject = subject, @emailbody = body from emailtemplate
			where emailcode = @BCEmailTemplate

		set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
		set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)

		insert into emailtable
		([from],[to],subject,body,createdDate,[toname]) values
		(@emailfrom, @bcemail, @emailsubject, @emailbody, @currentdt, @bcname)
	End
	
	-- Send email to Member -- 
	if ( ISNULL(@memberEmailTemplate, '') != '' AND ISNULL(@mbremail,'') != '' )
	Begin
		select @emailsubject = subject, @emailbody = body from emailtemplate
			where emailcode = @memberEmailTemplate

		set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
		set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)

		insert into emailtable
		([from],[to],subject,body,createdDate,[toname]) values
		(@emailfrom, @mbremail, @emailsubject, @emailbody, @currentdt, @bcname)
	End
	
	-- send SMS --
	if ( ISNULL(@sendSMStoo,0) = 1 AND ISNULL(@mbrphone,'') != '' )
	Begin
		
		select top 1 @smsbody = smsmsg from smsTemplate where smscode = @SMSTemplate
		
		set @smsbody = dbo.fn_setemailmsg(@smsbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
		
		insert into smsTable
			( phone, message, createdDate ) values 
			( @mbrphone, @smsbody, @currentdt)
	End
	
END
GO
/****** Object:  StoredProcedure [dbo].[sp_SalesConfirmQtyChange]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- call when member confirm the qty change
CREATE       procedure [dbo].[sp_SalesConfirmQtyChange]
	@salesid 	varchar(20)
as

update T1 set
	qty = (T1.qtybc + isnull(T2.qty,0)),
	totalorder = T1.price * (T1.qtybc + isnull(T2.qty,0)),
	discount = (T1.pricembr * (T1.qtybc + isnull(T2.qty,0))) - (T1.price * (T1.qtybc + isnull(T2.qty,0))),
	totalbayar = T1.pricembr * (T1.qtybc + isnull(T2.qty,0)) 
from salesline T1
left join purchline T2 with (nolock) on T1.salesid = T2.purchid and T1.itemid = T2.itemid
where T1.salesid = @salesid 

exec sp_updatesalestotal @salesid
GO
/****** Object:  StoredProcedure [dbo].[sp_updatePurchLine]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   procedure [dbo].[sp_updatePurchLine]
	@PurchID varchar(20)
as
delete PurchLine where PurchID = @PurchID

insert into purchline (purchid, itemid, qty, price, pricebc, rowid)
select T1.salesid, T1.itemid, 
	case 
		when T1.qty-T1.qtybc > 0 then 
			case 
				when T2.qtyonhand > T1.qty-T1.qtybc then T1.qty-T1.qtybc
				else T2.qtyonhand
			end 
		else 0
	end, 
	T2.pricecatalog, T2.pricebc, rowid
from salesline T1
inner join vw_inventtable T2 on T1.itemid = T2.itemid and T2.qtyonhand > 0
where T1.SalesID = @PurchID 

update purchLine set 
	totalorder = price * qty,
	discount = (pricebc * qty) - 	(price * qty),
	totalbayar = pricebc * qty
where PurchID = @PurchID
GO
/****** Object:  StoredProcedure [dbo].[sp_updateSalesStatus2]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    procedure [dbo].[sp_updateSalesStatus2]
	@salesid	varchar(20),
	@status 	smallint
as
Declare
	@bcstartwork	int,
	@bcendwork		int,
	@bcvalidate		int,
	@bcmaxvalidate	datetime,
	
	@mbrcode  		varchar(10),
	@mbrname		varchar(50),
	@mbremail		varchar(80),
	@mbrphone		varchar(50),
	@bccode  		varchar(10),
	@bcname			varchar(50),
	@bcemail		varchar(80),

	@emailfrom		varchar(80),
	@emailsubject	varchar(80),
	@emailbody		varchar(500),
	@BCemailsubject	varchar(80),
	@BCemailbody	varchar(500),

	@smsbody		varchar(500),

	@currentdt		datetime,
	@today			datetime,
	@timestamp 		bigint,
	@bypasseddate	datetime,
	@syncstatus		smallint,
	@responsecode	varchar(50),
	@salesidSMI		varchar(20),
	@purchPPN		decimal(18,3),
	
	@paymentmode	varchar(50),
	@virtualaccount	varchar(50),
	@msisdn			varchar(50);

if @status = 2 --ordered
begin
	set @today = dateadd(day,0,datediff(day,0,getdate())) --midnight
	select 
		@bcstartwork = bcstartwork,
		@bcendwork = bcendwork,
		@bcvalidate = bcvalidate,
		@emailfrom = emailfrom
	from sysparamtable with (nolock)

	set @currentdt = getdate()
	if dateadd(minute,@bcvalidate,@currentdt) < dateadd(minute,@bcendwork,@today)
	begin
		set @bcmaxvalidate = dateadd(minute,@bcvalidate,@currentdt)
	end else begin
		set @bcmaxvalidate = dateadd(minute,@bcvalidate,dateadd(minute,@bcstartwork,dateadd(day,1,@today)))  
	end

	update salestable set
		orderdate = @currentdt,
		maxvalidatedate = @bcmaxvalidate,
		status = @status
	where salesid = @salesid	


	-- send email to BC
	select 
		@mbrcode = kodemember,  		
		@mbrname = namamember,
		@bccode	 = kodebc,
		@bcname  = namabc,
		@bcemail = emailbc
	from vw_salestable with(nolock)
	where salesid = @salesid		
	
	select @emailsubject = subject, @emailbody = body from emailtemplate
	where emailcode = 'BC2VALIDATE'

	set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
	set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)

	insert into emailtable
	([from],[to],subject,body,createdDate,[toname]) values
	(@emailfrom, @bcemail, @emailsubject, @emailbody, @currentdt, @bcname)

end 

if @status = 4 --inprogress
begin
	delete purchline where purchid = @salesid and qty <= 0

	if exists(select purchid from purchline with(nolock) where purchid = @salesid and qty > 0)
	begin
		set @timestamp = dbo.Date2UnixTimeStamp(getdate())
		
		insert into syncordertable 
		(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail, total, force) 
		select @timestamp, @salesid, st.kodebc, st.kodemember, st.alamat, st.telp, st.email,
		pt.totalorder + pt.discount, CASE WHEN st.status = 3 THEN 1 ELSE 0 END
		from salestable st with(nolock) 
		inner join purchtable pt with (nolock)
			on st.salesid = pt.purchid
		where	st.salesid = @salesid 
		and		not exists ( select top 1 sessionid from syncordertable with (NOLOCK)
								where sessionid = @salesid )
		
		insert into syncorderline
		(timestamp, sessionid, rowid, itemid, price, qtyorder)
		select @timestamp, @salesid, rowid, itemid, price, qty from purchline as T1 with (NOLOCK)
		where	purchid = @salesid 
		and		qty		> 0
		and		not exists ( select top 1 sessionid from syncorderline with (NOLOCK)
								where	sessionid	= @salesid 
								and		itemid		= T1.itemid )
						
						
		insert into syncrequest (timestamp, sessionid, requestid, status) values
		(@timestamp, @salesid, 'order', 0)
	
		-- update purchtable
		update purchtable set 
			synctimestamp = @timestamp,
			status = 2, --purchOrdered,
			responsecode = null --kalo null berarti belum dapat feedback dari sync proses
		where purchid = @salesid
		
		-- update salestable
		update salestable set
			status = 4 --salesInProgress
		where salesid = @salesid	
	end
	else
	begin
		delete purchtable where purchid = @salesid 
	end
end 

if @status = 0 --salesCancelled
begin
	delete purchline where purchid = @salesid and qty <= 0

	if exists(select purchid from purchline with(nolock) where purchid = @salesid and qty > 0)
	begin
		set @timestamp = dbo.Date2UnixTimeStamp(getdate())
		
		insert into syncordertable 
		(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail) 
		select @timestamp, @salesid, kodebc, kodemember, alamat, telp, email
		from salestable with(nolock) where salesid = @salesid
		
		insert into syncorderline
		(timestamp, sessionid, rowid, itemid, price, qtyorder)
		select @timestamp, @salesid, rowid, itemid, price, qty from purchline with(nolock)
		where purchid = @salesid and qty > 0
		
		insert into syncrequest (timestamp, sessionid, requestid, status) values
		(@timestamp, @salesid, 'cancel', 0)
	
		-- update purchtable
		update purchtable set 
			synctimestamp = @timestamp,
			status = 0, --purchCancelled
			responsecode = null --kalo null berarti belum dapat feedback dari sync proses
		where purchid = @salesid
		
		-- update salestable
		update salestable set
			status = 0 --salesCancelled
		where salesid = @salesid	
	end
	else
	begin
		delete purchtable where purchid = @salesid 
	end
	
	update salestable set
		canceldate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		canceldate = getdate(),
		status = @status
	where purchid = @salesid

end 

if @status = 5 or @status = 6 --edited or validated
begin

	if exists(select status from salestable where salesid = @salesid and status = 4 ) --inprogress
	begin
	-- rule:
	-- syncrequest status 0:created, 1:inprocess, 2:success; -1:failure
	-- if no item has qtyimport status = failure
	-- if one or some item has qtyimport status = success
	
		select 
			@bypasseddate = bypasseddate,
			@timestamp = synctimestamp
		from purchtable with(nolock) where purchid = @salesid
	
		if exists(
			select timestamp
			from syncRequest with(nolock) 
			where timestamp = @timestamp and sessionid = @salesid and requestid = 'order')
		begin
			select
				@syncstatus = status,
				@responsecode = responsecode
			from syncRequest with(nolock) 
			where timestamp = @timestamp and sessionid = @salesid and requestid = 'order'
	
			if @syncstatus = 2 -- success
			begin
				select @salesidSMI = salesid from syncordertable with(nolock) 
				where timestamp = @timestamp and sessionid = @salesid
			
				select @purchPPN = bcincludeppn from sysparamtable with (nolock)
						
				update T1 set
					qty = T2.qtyimport,
					totalorder = T1.price * isnull(T2.qtyimport,0),
					discount = (T1.pricebc * isnull(T2.qtyimport,0)) - (T1.price * isnull(T2.qtyimport,0)),
					totalbayar = T1.pricebc * isnull(T2.qtyimport,0)
				from purchline T1 
				inner join syncorderline T2 on T1.purchid = T2.sessionid and T1.itemid = T2.itemid
				where T2.timestamp = @timestamp and T2.sessionid = @salesid
	
				update T1 set 
					status =  3, -- onOrder
					responsecode = @responsecode,
					salesidSMI = @salesidSMI,
					totalorder = T2.totalorder, 
					discount = T2.discount, 
					totalbayar = T2.totalbayar,
					includeppn = T2.totalbayar * @purchPPN / 100
				from purchtable T1 inner join						   
				(
					select purchid, sum(totalorder) as totalorder, sum(discount) as discount, sum(totalbayar) as totalbayar 
					from purchline where purchid = @salesid group by purchid 
				) T2 on T1.purchid = T2.purchid
				where T1.purchid = @salesid
	
				if exists(select salesid from vw_salesline where salesid = @salesid and shortageqty > 0)
				begin
					update salestable set status = 5, edited=1, editeddate=GETDATE() --edited
					where salesid = @salesid
	
					select @emailsubject = subject, @emailbody = body from emailtemplate
					where emailcode = 'EDTORD2MBR'

				end else begin
					update salestable set status = 6, edited=0, validatedate=GETDATE() --validated
					where salesid = @salesid
	
					select @emailsubject = subject, @emailbody = body from emailtemplate
					where emailcode = 'VLDORD2MBR'
				end
	
				-- send email to member
				select 
					@mbrcode = kodemember,  		
					@mbrname = namamember,
					@mbremail = email,
					@mbrphone = telp,
					@bccode	 = kodebc,
					@bcname  = namabc,
					@bcemail = emailbc
				from vw_salestable with(nolock)
				where salesid = @salesid		

				-- email to member
				set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
				set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
			
				insert into emailtable
				([from],[to],subject,body,createdDate,[toname]) values
				(@emailfrom, @bcemail, @emailsubject, @emailbody, @currentdt, @mbrname)
				
				-- SMS to member
				set @smsbody = dbo.fn_setemailmsg(@smsbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
				insert into 
					smsTable( phone, message, createdDate ) values 
					( @mbrphone, @smsbody, @currentdt)

				-- Create Payment Data --
				select top 1 @paymentmode=paymentMode, @virtualaccount=virtualaccount, @msisdn=PaymentMobileNumber
					from salesTable with (NOLOCK) where salesid=@salesid
					
				exec sp_paygate_PaymCreate @salesid, @paymentmode, @virtualaccount, @msisdn
	
			end
	
			if @syncstatus = -1 -- Failure
			begin
				if @bypasseddate is null --sync by BC
				begin
	
					update salestable set status = 2 --ordered
					where salesid = @salesid
	
					update purchtable set status = 1 --openorder
					where purchid = @salesid
	
				end else begin --sync because bypassed
	
					update salestable set status = 0 --cancelled
					where salesid = @salesid
	
					update purchtable set status = 0 --cancelled
					where purchid = @salesid
	
					select @emailfrom = emailfrom from sysparamtable with (nolock)
	
					-- send email			
					select 
						@mbrcode = kodemember,  		
						@mbrname = namamember,
						@mbremail = email,
						@mbrphone = telp,
						@bccode	 = kodebc,
						@bcname  = namabc,
						@bcemail = emailbc
					from vw_salestable with(nolock)
					where salesid = @salesid		
	
					-- email to BC
					select @emailsubject = subject, @emailbody = body from emailtemplate
					where emailcode = 'SMIINSFTQTY2BC'
					
					set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
					set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
				
					insert into emailtable
					([from],[to],subject,body,createdDate,[toname]) values
					(@emailfrom, @bcemail, @emailsubject, @emailbody, @currentdt, @bcname)

					-- email to member
					select @emailsubject = subject, @emailbody = body from emailtemplate
					where emailcode = 'SMIINSFTQTY2MBR'
					
					set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
					set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
				
					insert into emailtable
					([from],[to],subject,body,createdDate,[toname]) values
					(@emailfrom, @bcemail, @emailsubject, @emailbody, @currentdt, @mbrname)
					
					-- SMS to member
					select top 1 @smsbody = smsmsg from smsTemplate where smscode = 'SMIINSFTQTY2MBR'
					
					set @smsbody = dbo.fn_setemailmsg(@smsbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
					insert into smsTable
					( phone, message, createdDate ) values 
					( @mbrphone, @smsbody, @currentdt)
					
				end 
			end 
		end
	end
	else
	begin
		if @status = 6 --validated
		begin
			update salestable set
				validatedate = getdate(),
				status = @status
			where salesid = @salesid
		end
	end
end 


if @status = 7 --confirmed
begin
	update salestable set
		confirmeddate = getdate(),
		status = @status
	where salesid = @salesid	
	
	-- Initialize payment --
	exec sp_paygate_PaymInit @salesid, '', ''
end 

if @status = 8 --paid
begin
	update salestable set
		paiddate = getdate(),
		status = @status
	where salesid = @salesid	
end 

if @status = 9 --delivered
begin
	update salestable set
		deliverdate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		deliverdate = getdate(),
		status = @status
	where purchid = @salesid	
end 

if @status = 10 --cleared
begin
	update salestable set
		cleardate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		cleardate = getdate(),
		status = @status
	where purchid = @salesid
end
GO
/****** Object:  StoredProcedure [dbo].[sp_updateSalesStatus]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE      procedure [dbo].[sp_updateSalesStatus]
	@salesid	varchar(20),
	@status 	smallint,
	@cancelcode smallint = 0
as
Declare
	@bcstartwork	int,
	@bcendwork		int,
	@bcvalidate		int,
	@bcmaxvalidate	datetime,
	
	@emailTemplate	varchar(50),
	@SMSTemplate	varchar(50),

	@currentdt		datetime,
	@today			datetime,
	@timestamp 		bigint,
	@bypasseddate	datetime,
	@syncstatus		smallint,
	@responsecode	varchar(50),
	@salesidSMI		varchar(20),
	@purchPPN		decimal(18,3),
	
	@paymentmode	varchar(50),
	@virtualaccount	varchar(50),
	@msisdn			varchar(50);

if @status = 2 --ordered
begin
	set @today = dateadd(day,0,datediff(day,0,getdate())) --midnight
	select 
		@bcstartwork = bcstartwork,
		@bcendwork = bcendwork-bcvalidate,
		@bcvalidate = bcvalidate
	from sysparamtable with (nolock)

	set @currentdt = getdate()
	if dateadd(minute,@bcvalidate,@currentdt) < dateadd(minute,@bcendwork,@today)
	begin
		set @bcmaxvalidate = dateadd(minute,@bcvalidate,@currentdt)
	end else begin
		set @bcmaxvalidate = dateadd(minute,@bcvalidate,dateadd(minute,@bcstartwork,dateadd(day,1,@today)))  
	end

	update salestable set
		orderdate = @currentdt,
		maxvalidatedate = @bcmaxvalidate,
		status = @status
	where salesid = @salesid	


	-- send email to BC
	exec sp_sendEmailAndSMS @salesid, 0 , '', 'BC2VALIDATE', ''

end 

if @status = 4 --inprogress
begin
	delete purchline where purchid = @salesid and qty <= 0

	if exists(select purchid from purchline with(nolock) where purchid = @salesid and qty > 0)
	begin
		set @timestamp = dbo.Date2UnixTimeStamp(getdate())
		
		insert into syncordertable 
		(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail, total, force) 
		select @timestamp, @salesid, st.kodebc, st.kodemember, st.alamat, st.telp, st.email,
		pt.totalorder + pt.discount, CASE WHEN st.status = 3 THEN 1 ELSE 0 END
		from salestable st with(nolock) 
		inner join purchtable pt with (nolock)
			on st.salesid = pt.purchid
		where	st.salesid = @salesid 
		and		not exists ( select top 1 sessionid from syncordertable with (NOLOCK)
								where sessionid = @salesid )
		
		insert into syncorderline
		(timestamp, sessionid, rowid, itemid, price, qtyorder)
		select @timestamp, @salesid, rowid, itemid, price, qty from purchline as T1 with (NOLOCK)
		where	purchid = @salesid 
		and		qty		> 0
		and		not exists ( select top 1 sessionid from syncorderline with (NOLOCK)
								where	sessionid	= @salesid 
								and		itemid		= T1.itemid )
						
						
		insert into syncrequest (timestamp, sessionid, requestid, status) values
		(@timestamp, @salesid, 'order', 0)
	
		-- update purchtable
		update purchtable set 
			synctimestamp = @timestamp,
			status = 2, --purchOrdered,
			responsecode = null --kalo null berarti belum dapat feedback dari sync proses
		where purchid = @salesid
		
		-- update salestable
		update salestable set
			status = 4 --salesInProgress
		where salesid = @salesid	
	end
	else
	begin
		delete purchtable where purchid = @salesid 
	end
end 

if @status = 0 --salesCancelled
begin
	delete purchline where purchid = @salesid and qty <= 0

	if exists(select purchid from purchline with(nolock) where purchid = @salesid and qty > 0)
	begin
		set @timestamp = dbo.Date2UnixTimeStamp(getdate())
		
		insert into syncordertable 
		(timestamp, sessionid, kodebc, kodemember, deliveryaddress, deliveryphone, deliveryemail) 
		select @timestamp, @salesid, kodebc, kodemember, alamat, telp, email
		from salestable with(nolock) where salesid = @salesid
		
		insert into syncorderline
		(timestamp, sessionid, rowid, itemid, price, qtyorder)
		select @timestamp, @salesid, rowid, itemid, price, qty from purchline with(nolock)
		where purchid = @salesid and qty > 0
		
		insert into syncrequest (timestamp, sessionid, requestid, status) values
		(@timestamp, @salesid, 'cancel', 0)
	
		-- update purchtable
		update purchtable set 
			synctimestamp = @timestamp,
			status = 0, --purchCancelled
			responsecode = null --kalo null berarti belum dapat feedback dari sync proses
		where purchid = @salesid
		
		-- update salestable
		update salestable set
			status = 0, --salesCancelled
			cancelcode = @cancelcode 
		where salesid = @salesid	
	end
	else
	begin
		delete purchtable where purchid = @salesid 
	end
	
	update salestable set
		canceldate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		canceldate = getdate(),
		status = @status
	where purchid = @salesid

end 

if @status = 5 or @status = 6 --edited or validated
begin

	if exists(select status from salestable where salesid = @salesid and status = 4 ) --inprogress
	begin
	-- rule:
	-- syncrequest status 0:created, 1:inprocess, 2:success; -1:failure
	-- if no item has qtyimport status = failure
	-- if one or some item has qtyimport status = success
	
		select 
			@bypasseddate = bypasseddate,
			@timestamp = synctimestamp
		from purchtable with(nolock) where purchid = @salesid
	
		if exists(
			select timestamp
			from syncRequest with(nolock) 
			where timestamp = @timestamp and sessionid = @salesid and requestid = 'order')
		begin
			select
				@syncstatus = status,
				@responsecode = responsecode
			from syncRequest with(nolock) 
			where timestamp = @timestamp and sessionid = @salesid and requestid = 'order'

			if @syncstatus = 2 -- success
			begin
				select @salesidSMI = salesid from syncordertable with(nolock) 
				where timestamp = @timestamp and sessionid = @salesid
			
				select @purchPPN = bcincludeppn from sysparamtable with (nolock)
						
				update T1 set
					qty = T2.qtyimport,
					totalorder = T1.price * isnull(T2.qtyimport,0),
					discount = (T1.pricebc * isnull(T2.qtyimport,0)) - (T1.price * isnull(T2.qtyimport,0)),
					totalbayar = T1.pricebc * isnull(T2.qtyimport,0)
				from purchline T1 
				inner join syncorderline T2 on T1.purchid = T2.sessionid and T1.itemid = T2.itemid
				where T2.timestamp = @timestamp and T2.sessionid = @salesid
	
				update T1 set 
					status =  3, -- onOrder
					responsecode = @responsecode,
					salesidSMI = @salesidSMI,
					totalorder = T2.totalorder, 
					discount = T2.discount, 
					totalbayar = T2.totalbayar,
					includeppn = T2.totalbayar * @purchPPN / 100
				from purchtable T1 inner join						   
				(
					select purchid, sum(totalorder) as totalorder, sum(discount) as discount, sum(totalbayar) as totalbayar 
					from purchline where purchid = @salesid group by purchid 
				) T2 on T1.purchid = T2.purchid
				where T1.purchid = @salesid
	
				if exists(select salesid from vw_salesline where salesid = @salesid and shortageqty > 0)
				begin
					update salestable set status = 5, edited=1, editeddate=GETDATE() --edited
					where salesid = @salesid
				
					set @emailTemplate = 'EDTORD2MBR'
					set @SMSTemplate = 'EDTORD2MBR'

				end else begin
					update salestable set status = 6, edited=0, validatedate=GETDATE() --validated
					where salesid = @salesid
						
					set @emailTemplate = 'VLDORD2MBR'
					set @SMSTemplate = 'VLDORD2MBR'
				end
	
				-- send email and SMS to member
				exec sp_sendEmailAndSMS @salesid, 1 , @emailTemplate, '', @SMSTemplate

				-- Create Payment Data --
				if not exists(select top 1 salesid from paymenttable with (NOLOCK) where salesid = @salesid)
				begin
					select top 1 @paymentmode=paymentMode, @virtualaccount=virtualaccount, @msisdn=PaymentMobileNumber
						from salesTable with (NOLOCK) where salesid=@salesid
						
					exec sp_paygate_PaymCreate @salesid, @paymentmode, @virtualaccount, @msisdn
				end
			end
	
			if @syncstatus = -1 -- Failure
			begin
				if @bypasseddate is null --sync by BC
				begin
	
					update salestable set status = 2 --ordered
					where salesid = @salesid
	
					update purchtable set status = 1 --openorder
					where purchid = @salesid
					
					-- send email and SMS to BC
					exec sp_sendEmailAndSMS @salesid, 0, '', 'SMIINSFTQTY2BC', ''
	
				end else begin --sync because bypassed
	
					update salestable set status = 0 --cancelled
					where salesid = @salesid
	
					update purchtable set status = 0 --cancelled
					where purchid = @salesid
	
					-- send email and SMS to Member
					exec sp_sendEmailAndSMS @salesid, 1 , 'SMIINSFTQTY2MBR', '', 'SMIINSFTQTY2MBR'
				end 
			end 
		end
	end
	else
	begin
		if exists(select salesid from vw_salesline where salesid = @salesid and shortageqty > 0)
		begin
			set @status = 5
		end
		else
		begin
			set @status = 6
		end
		
		update salestable set
			validatedate = getdate(),
			status = @status
		where salesid = @salesid
		
		-- Create Payment Data --
		if not exists(select top 1 salesid from paymenttable with (NOLOCK) where salesid = @salesid)
		begin
			select top 1 @paymentmode=paymentMode, @virtualaccount=virtualaccount, @msisdn=PaymentMobileNumber
				from salesTable with (NOLOCK) where salesid=@salesid
				
			exec sp_paygate_PaymCreate @salesid, @paymentmode, @virtualaccount, @msisdn
		end 
	end
end 


if @status = 7 --confirmed
begin
	update salestable set
		confirmeddate = getdate(),
		status = @status
	where salesid = @salesid	
	
	-- Initialize payment --
	exec sp_paygate_PaymInit @salesid, '', ''
end 

if @status = 8 --paid
begin
	update salestable set
		paiddate = getdate(),
		status = @status
	where salesid = @salesid	
end 

if @status = 9 --ready
begin
	update salestable set
		status = @status
	where salesid = @salesid	
end 


if @status = 10 --delivered
begin
	update salestable set
		deliverdate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		deliverdate = getdate(),
		status = @status
	where purchid = @salesid	
end 

if @status = 11 --cleared
begin
	update salestable set
		cleardate = getdate(),
		status = @status
	where salesid = @salesid	

	update purchtable set
		cleardate = getdate(),
		status = @status
	where purchid = @salesid
end
GO
/****** Object:  StoredProcedure [dbo].[sp_checkSyncOrder]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE procedure [dbo].[sp_checkSyncOrder]
as
declare 
	@requestid			varchar(10),
	@shortageqty		int,

	@timestamp			bigint,
	@purchId			varchar(20),
	@syncStatus			smallint,
	@syncResponseCode	varchar(50),
	@AXSalesID			varchar(20)

set @requestid = 'order'

if exists(
	select T1.purchid 
	from purchtable T1 with(nolock)
	inner join syncrequest T2 with(nolock) on 
		T1.purchid = T2.sessionid and 
		T1.synctimestamp = T2.timestamp
	where 
		T1.status = 2 --purchOrdered
		and not T1.synctimestamp is null
		and T1.responsecode is null
		and T2.requestid = @requestid
		and (T2.status = 2 or T2.status = 3 or T2.status = -1) -- success / edited / failed
) 
begin

	declare mycursor cursor READ_ONLY for
		select 
			T1.synctimestamp, 
			T1.purchid, 
			T2.status, 
			T3.ResponseCode, 
			T3.salesid 
		from purchtable T1 with(nolock)
		inner join syncrequest T2 with(nolock) on 
			T1.purchid = T2.sessionid and 
			T1.synctimestamp = T2.timestamp
		inner join syncOrderTable T3 with(nolock) on 
			T1.purchid = T3.sessionid and 
			T2.timestamp = T3.timestamp
		where 
			T1.status = 2 --purchOrdered
			and not T1.synctimestamp is null
			and T1.responsecode is null
			and T2.requestid = @requestid
			and (T2.status = 2 or T2.status = 3 or T2.status = -1) -- success / edited / failed

	open mycursor
	fetch next from mycursor into @timestamp, @purchId, @syncStatus, @syncResponseCode, @AXSalesID			
	while @@FETCH_STATUS = 0
	begin
		-- update responsecode for purchline
		update T1 set 
			responseCode = T2.responsecode
		from purchline T1
		inner join syncOrderLine T2 with(nolock) on 
			T2.sessionid = @purchId
			and T2.timestamp = @timestamp 
			and T2.Itemid = T1.itemid
		where T1.purchid = @purchId

		-- update purchtable
		update purchtable set
			responseCode = @syncResponseCode,
			salesidsmi = @AXSalesID,
			status = 
					case @syncStatus
						when 2 then 3 --purchOnOrder
						when 3 then 3 --purchOnOrder
						else 1 --purchOpenOrder 					
					end 
		where purchid = @purchId

		-- update salestable
		if @syncStatus <> -1 -- tidak fail
		begin
			set @shortageqty = (select sum(shortageqty) as totalshortageqty from vw_validateorderd where salesid = @purchid)

			update salestable set
				status =
						case @shortageqty
							when 0 then 6 --salesValidated
							else 5 --salesEdited
						end 
			where salesid = @purchid		
		end 
		else
		begin		
			update salestable set
				status = 2 --salesOrdered
			where salesid = @purchid	
		end 
		fetch next from mycursor into @timestamp, @purchId, @syncStatus, @syncResponseCode, @AXSalesID			
	end 

	close mycursor
	deallocate mycursor

	-- gimana sales order yang bypassed dan gagal sync ?
    -- repeat sampai kapan ?
end
GO
/****** Object:  StoredProcedure [dbo].[sp_cancelSalesNotYetPaid]    Script Date: 06/08/2013 20:35:33 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		Ivan Widjaksono
-- ALTER  date: 
-- Description:	
-- =============================================
CREATE PROCEDURE [dbo].[sp_cancelSalesNotYetPaid] 

AS
BEGIN

	Declare @salesId		varchar(50),
	
			@mbrcode  		varchar(10),
			@mbrname		varchar(50),
			@mbremail		varchar(80),
			@mbrphone		varchar(50),
			@bccode  		varchar(10),
			@bcname			varchar(50),
			@bcemail		varchar(80),

			@emailfrom		varchar(80),
			@emailsubject	varchar(80),
			@emailbody		varchar(500)

	select top 1
		@emailfrom = emailfrom
	from sysparamtable with (nolock)

	Declare loopCancel Cursor For
		Select SalesId from SalesTable with (NOLOCK) where Status in (5,6) and DateDiff(mi,maxpaiddate, getdate()) >= 1
	Open loopCancel

	Fetch Next From loopCancel into @salesId

	While @@Fetch_Status = 0 
	Begin
	
		exec sp_updateSalesStatus @salesId, 0, 2 --cancelcode because late payment

		-- email to member --
		select top 1 
			@mbrcode = kodemember,  		
			@mbrname = namamember,
			@mbremail = email,
			@mbrphone = telp,
			@bccode	 = kodebc,
			@bcname  = namabc,
			@bcemail = emailbc
		from vw_salestable with(nolock)
		where salesid = @salesId	
		
		select @emailsubject = subject, @emailbody = body from emailtemplate
		where emailcode = 'NOTPAID2MBR'
		
		set	@emailsubject = dbo.fn_setemailmsg(@emailsubject,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
		set	@emailbody = dbo.fn_setemailmsg(@emailbody,@Salesid,@mbrcode,@mbrname,@bccode,@bcname)
	
		insert into emailtable
		([from],[to],subject,body,createdDate,[toname]) values
		(@emailfrom, @mbremail, @emailsubject, @emailbody, getdate(), @mbrname)

		Fetch Next From loopCancel into @salesId
	End

	Close loopCancel
	Deallocate loopCancel

END
GO
