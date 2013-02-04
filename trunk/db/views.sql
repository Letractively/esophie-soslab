SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_BCMapping]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE view [dbo].[vw_BCMapping]
as
select 
	T2.KodeMember, T1.kodebc, T1.kodebc + '' - '' + T1.namabc as label, 
	T1.namabc, T1.alamat, T1.telp, T1.email,T2.defaultbc
from BCTable T1 with(nolock)
inner join mappingTable T2 with(nolock) on T1.kodebc = T2.kodebc
where T1.suspend = 0
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_member]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE VIEW [dbo].[vw_member]
AS
SELECT     kodemember AS KodeMember, name AS namaMember, address AS Alamat, phone AS Telp, email
FROM         dbo.memberTable
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_salestable]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE view [dbo].[vw_salestable]
as
select 
	T1.salesid, T1.orderdate, T1.kodemember, T1.namamember, T1.alamat, T1.telp, T1.email,
	T1.totalorder, T1.discount, T1.totalbayar, T1.paymentcharge,
	isnull(T4.totalorder,0) as totalorderbc, isnull(T4.discount,0) as discountbc, isnull(T4.totalbayar,0) as totalbayarbc,  
	T2.kodebc, T2.namabc, T2.alamat as alamatbc, T2.telp as telpbc, T2.email as emailbc,
	dbo.fn_salesuserstatus(T1.status) as userstatus, T1.status,
	T1.paymentmode,
	isnull(T3.shortname,'''') as paymentname,
	virtualaccount,
	case 
			when T1.status between 4 and 8 and maxpaiddate > getdate() then 
				case 
					when datediff(mi, getdate(), maxpaiddate) >= 60 then
						cast(datediff(mi, getdate(), maxpaiddate) / 60 as varchar(3)) + '' Jam ''
					else ''''
				end 
				+ cast(datediff(mi, getdate(), maxpaiddate) % 60 as varchar(3)) + '' Menit''
			else ''0 Menit'' 
		end as timeleft,
	T5.statusinfo,
	case when DATEADD(dd, DATEDIFF(dd, 0, getdate()), 0) = DATEADD(dd, DATEDIFF(dd, 0, T1.MaxValidateDate), 0) then 0
	else 1 
	end as validatesameday
from salestable T1 with (nolock)
inner join BCTable T2 with (nolock) on T1.kodebc = T2.kodebc
left join PaymentMode T3 with (nolock) on T1.paymentmode = T3.paymentmode
left join PurchTable T4 with (nolock) on T1.SalesId = T4.PurchID
left join SyncRequest T5 with (NOLOCK) on T1.SalesId = T5.SessionId
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_sosstatussync]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE        VIEW [dbo].[vw_sosstatussync]
AS
SELECT  dbo.salesTable.salesid, 
	dbo.salesTable.kodemember, 
	dbo.salesTable.namamember, 
	dbo.salesTable.alamat, 
	dbo.salesTable.telp, 
	dbo.salesTable.email, 
	dbo.salesTable.kodebc, 
	dbo.salesTable.status, 
	dbo.salesTable.edited, 
    dbo.salesTable.totalbayar,
    dbo.salesTable.totalorder,
	DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.orderdate), 0) AS createdDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.orderdate) * - 1, dbo.salesTable.orderdate)) AS createdTime,                      
	DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.validatedate), 0) AS validateDate,
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.validatedate) * - 1, dbo.salesTable.validatedate)) AS validateTime, 
	dbo.salesTable.paymentcharge,	
	dbo.paymentTable.paymentmode,
	dbo.paymentTable.trxref,
	dbo.paymentTable.trxdate,
	dbo.paymentTable.reference,
	dbo.paymentTable.paymstatus,
	DATEADD(dd, DATEDIFF(dd, 0, dbo.paymentTable.initializedate), 0) AS initializeDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.paymentTable.initializedate) * - 1, dbo.paymentTable.initializedate)) AS initializeTime, 
	DATEADD(dd, DATEDIFF(dd, 0, dbo.paymentTable.confirmeddate), 0) AS confirmedDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.paymentTable.confirmeddate) * - 1, dbo.paymentTable.confirmeddate)) AS confirmedTime, 
	CASE WHEN dbo.paymentTable.minpaiddate >= GETDATE() THEN DATEDIFF(SECOND, GETDATE(), dbo.paymentTable.minpaiddate) ELSE 0 END AS paymtimemin, 
	CASE WHEN dbo.paymentTable.maxpaiddate >= GETDATE() THEN DATEDIFF(SECOND, GETDATE(), dbo.paymentTable.maxpaiddate) ELSE 0 END  AS paymtimeleft,
	DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.paiddate), 0) AS paidDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.paiddate) * - 1, dbo.salesTable.paiddate)) AS paidTime,
	dbo.purchTable.salesidSMI, 
	dbo.purchTable.totalbayar AS purchtotalbayar, 
        	dbo.purchTable.status AS purchstatus,
	DATEADD(dd, DATEDIFF(dd, 0, GETDATE()), 0) AS currentDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, GETDATE()) * - 1, GETDATE())) AS currentTime 
FROM dbo.salesTable 
LEFT OUTER JOIN
      dbo.purchTable ON dbo.salesTable.salesid = dbo.purchTable.purchid
LEFT OUTER JOIN
      dbo.paymentTable ON dbo.salesTable.salesid = dbo.paymentTable.salesid
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_salesline]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE           view [dbo].[vw_salesline]
as
select 
	T1.salesid, T1.itemid, T2.itemname, T1.qty, T1.price, T1.pricembr,  
	T1.totalorder, T1.discount , T1.totalbayar,
	T1.qtybc,isnull(T3.qty,0) as purchqty, 
	T1.Qty - (T1.QtyBC + isnull(T3.qty,0)) as shortageqty,
	isnull(T3.totalbayar,0) as totalbayarbc,
	(T1.QtyBC + isnull(T3.qty,0)) as qtyedited,
	(T1.qtybc+isnull(T3.qty,0))*T1.price as totalorderedited, 
	(T1.qtybc+isnull(T3.qty,0))*T1.pricembr - (T1.qtybc+isnull(T3.qty,0))*T1.price as discountedited, 
	(T1.qtybc+isnull(T3.qty,0))*T1.pricembr as totalbayaredited 
from salesLine T1 with (nolock)
inner join InventTable T2 with (nolock) on T1.itemid= T2.itemid
left join PurchLine T3 with (nolock) on T3.PurchId = T1.SalesID and T3.itemid = T1.itemid
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_inventtable]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE      view [dbo].[vw_inventtable]
as
Select T1.itemid, T1.itemname, T1.qty, T1.pricecatalog,
	T1.pricebc, T1.pricembr, T1.deadstyle, T1.qty - isnull(T2.qty,0) as qtyonhand
from InventTable T1 with (nolock)
left join 
(
	select T1.itemid, sum(T1.qty) as qty from PurchLine T1 --with (nolock) 
	inner join SalesTable T2  with (nolock) on T1.PurchId = T2.SalesId 
	where T2.Status = 4 --inprogress
	group by T1.itemid
) T2 on T1.itemid = T2.itemid
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_purchline]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE       view [dbo].[vw_purchline]
as
select 
	T1.purchid, T1.itemid, T2.itemname, T1.qty, T1.price, T1.priceBC,  
	T1.totalorder, T1.discount , T1.totalbayar
from PurchLine T1 with (nolock)
inner join InventTable T2 with (nolock) on T1.itemid= T2.itemid
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_myonlineorder]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE VIEW [dbo].[vw_myonlineorder]
AS
	select T1.KodeBC, T1.purchid, T1.salesidsmi, T1.orderdate, T1.totalbayar, T1.status, 
			case when isnull(T2.StatusInfo,'''') != '''' then dbo.fn_purchuserstatus(T1.status) + '' - '' + T2.StatusInfo
			else dbo.fn_purchuserstatus(T1.status) 
			end	as userstatus
		from purchtable as T1
		left join syncRequest as T2 with (NOLOCK) 
			on T1.PurchId = T2.SessionId and T2.requestid = ''order''
	where	T1.cleardate is null 
	and		exists ( select top 1 purchId from PurchLine with (NOLOCK) 
						where PurchId=T1.PurchId and (totalBayar>0 or qty>0) )
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_report03]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
create view [dbo].[vw_report03] as 
select 
	T2.salesid, T2.orderdate, T2.kodemember, T2.namamember,
	T2.totalbayar as totalbayarmbr, T2.paymentcharge, 
	T1.salesidsmi, T1.totalbayar as totalbayarbc, 
	T2.totalbayar - T2.paymentcharge - T1.totalbayar as kreditbc,
	dbo.fn_purchuserstatus(T1.status) as statusname,
	T1.kodebc, T1.Status
from PurchTable T1
inner join SalesTable T2 on T1.PurchID = T2.SalesID' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_purchtable]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE    view [dbo].[vw_purchtable]
as
select T1.purchid, T1.salesidsmi, T1.orderdate, T1.totalorder, T1.discount, T1.totalbayar, T1.includeppn,
T2.kodebc, T2.namabc, T2.alamat as alamatbc, T2.telp as telpbc, T2.email as emailbc,
T1.status, 
case when isnull(T3.StatusInfo,'''') != '''' then dbo.fn_purchuserstatus(T1.status) + '' - '' + T3.StatusInfo
else dbo.fn_purchuserstatus(T1.status) 
end	as userstatus
from purchtable T1 with (nolock)
inner join BCTable T2 with (nolock) on T1.kodebc = T2.kodebc
left join SyncRequest T3 with (NOLOCK) on T1.PurchId = T3.SessionId and T3.requestid =''order''
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_report02]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
create view [dbo].[vw_report02] as 
select 
	T1.salesid, T1.orderdate, T1.kodemember, T1.namamember,
	T1.totalbayar, T1.paymentcharge, 
	isnull(T2.salesidsmi,'''') as salesidsmi, dbo.fn_salesuserstatus(T1.status) as statusname,
	T1.kodebc, T1.Status
from SalesTable T1
left join PurchTable T2 on T1.salesid = T2.purchid
where T1.Status in (0,10)' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_validateorderh]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE         view [dbo].[vw_validateorderh]
as
select 
	T1.salesid, T1.kodemember, T1.namamember, T1.orderDate, T1.totalbayar as totalbayarmember,
	dbo.fn_salesuserstatus(T1.status) as userstatus, T1.status,
	isnull(T2.totalbayar,0) as totalbayarbc, T1.kodebc
from SalesTable T1 with (nolock) 
left join PurchTable T2 with (nolock) on T1.SalesId = T2.PurchID
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_report01]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
create view [dbo].[vw_report01] as 
select T1.itemid, T3.itemname, T1.qtybc, T1.salesid, T2.kodemember, T2.kodebc, dbo.fn_salesuserstatus(T2.status) as status 
from salesline T1
inner join salestable T2 on T1.salesid = T2.salesid
inner join inventtable T3 on T1.itemid = T3.itemid
where T2.status in (6,7,8)' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_onlineorder]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE          view [dbo].[vw_onlineorder]
as
select T1.salesid, T1.orderdate, T1.kodemember, T1.kodebc, T1.totalorder, T1.discount, T1.totalbayar, 
case 
	when T1.status = 2 and T1.maxvalidatedate > getdate() then 
		case 
			when datediff(mi, getdate(), T1.maxvalidatedate) >= 60 then
				cast(datediff(mi, getdate(), T1.maxvalidatedate) / 60 as varchar(3)) + '' Jam ''
			else ''''
		end 
		+ cast(datediff(mi, getdate(), T1.maxvalidatedate) % 60 as varchar(3)) + '' Menit''
	else ''0 Menit'' 
end as timeleft,
T1.status,dbo.fn_salesuserstatus(T1.status) as userstatus, 
T1.cleardate,
case 
	when T1.status = 2 then 1
	when T1.status = 3 or T1.status = 4 then 2
	when T1.status = 8 then 3
	when (T1.status = 6 or T1.status = 7) then 4
	when T1.status = 5 then 7
	when T1.status = 0 then 8
	when T1.status = 9 then 9
end as priority,
case ISNULL(T2.Status,0) 
	when 0 then ''Waiting''
	when 1 then ''Error'' + ISNULL(''-'' + T2.StatusInfo, '''')
	when 2 then ''Success''
end as syncstatus, ISNULL(T2.Status,0) as syncstatuscode
from salestable as T1 with (nolock)
left join syncRequest as T2 with (NOLOCK)
	on T1.SalesId = T2.SessionId and T2.requestid = ''order''
where T1.status <> 1 -- Open Order
and T1.cleardate is null
' 

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[vw_validateorderd]') AND OBJECTPROPERTY(id, N'IsView') = 1)
EXEC dbo.sp_executesql @statement = N'
CREATE          view [dbo].[vw_validateorderd]
as
select 
	T1.Salesid, T1.itemid, T2.itemname, T1.price,T1.pricembr, T1.qty as salesqty, 
	T1.totalorder as totalordermember, T1.discount as discountmember, T1.totalbayar as totalbayarmember,
	T1.qtybc as qtybc,isnull(T3.qty,0) as purchqty, T1.shortageqty,
	isnull(T3.totalbayar,0) as totalbayarbc
from vw_SalesLine T1 with (nolock)
inner join InventTable T2 with (nolock) on T1.itemid= T2.itemid
left join PurchLine T3 with (nolock) on T3.PurchId = T1.SalesID and T3.itemid = T1.itemid
' 
