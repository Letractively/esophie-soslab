
/****** Object:  View [dbo].[vw_sosstatussync]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO
-- =============================================
-- ALTER  view basic template
-- =============================================


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
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, GETDATE()) * - 1, GETDATE())) AS currentTime, dbo.salesTable.cancelcode,
	DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.deliverdate), 0) AS deliverDate, 
	DATEDIFF(ss, 0, DATEADD(dd, DATEDIFF(dd, 0, dbo.salesTable.deliverdate) * - 1, dbo.salesTable.deliverdate)) AS deliverTime

FROM dbo.salesTable 
LEFT OUTER JOIN
      dbo.purchTable ON dbo.salesTable.salesid = dbo.purchTable.purchid
LEFT OUTER JOIN
      dbo.paymentTable ON dbo.salesTable.salesid = dbo.paymentTable.salesid
GO
/****** Object:  View [dbo].[vw_paymtable]    Script Date: 07/01/2013 11:34:54 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[vw_paymtable]
AS
SELECT     T1.salesid, T1.orderdate, T1.kodemember, T1.namamember, T1.alamat, T1.totalorder, T1.discount, T1.totalbayar, T1.paymentcharge, 
                      T2.kodebc, T2.namabc, T2.alamat AS alamatbc, dbo.fn_salesuserstatus(T1.status) AS userstatus, T1.status, T1.paymentmode, 
                      ISNULL(T3.shortname, '') AS paymentname, T3.merchantid, T3.currencycode, T3.returnurl, T3.password, T3.paymentto, T3.description,
					  T1.virtualaccount, T1.validatedate, T4.paymstatus, T4.initializedate, T4.trxref, T4.trxdate, CASE WHEN T4.maxpaymdate IS NOT NULL 
					  AND getdate() < T4.maxpaymdate THEN datediff(mi, getdate(), T4.maxpaymdate) ELSE 0 END AS timeleftinit, T4.maxpaiddate, CASE WHEN NOT T4.maxpaiddate IS NULL 
                      THEN CASE WHEN datediff(mi, getdate(), T4.maxpaiddate) >= 60 THEN CAST(datediff(mi, getdate(), T4.maxpaiddate) / 60 AS varchar(3)) 
                      + ' Jam ' ELSE '' END + CAST(datediff(mi, getdate(), T4.maxpaiddate) % 60 AS varchar(3)) + ' Menit' ELSE '0 Menit' END AS timeleftpaid
FROM         dbo.salesTable AS T1 WITH (nolock) LEFT OUTER JOIN
                      dbo.BCTable AS T2 WITH (nolock) ON T1.kodebc = T2.kodebc LEFT OUTER JOIN
                      dbo.paymentMode AS T3 WITH (nolock) ON T1.paymentmode = T3.paymentmode LEFT OUTER JOIN
					  dbo.paymentTable AS T4 WITH (nolock) ON T1.salesid = T4.salesid
GO
/****** Object:  View [dbo].[vw_salestatusperbc]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   view [dbo].[vw_salestatusperbc] as 
select
	kodebc,
        SUM(CASE WHEN status = 0 AND cleardate IS NOT NULL THEN 1 ELSE 0 END) AS batalclear, 
	SUM(CASE WHEN status = 0 AND cleardate IS NULL THEN 1 ELSE 0 END) AS batal,
	sum(case when status = 2 then 1 else 0 end) as orderbaru, 
	sum(case status when 3 then 1 when 4 then 1 else 0 end) as dalamproses,
	sum(case when status = 5 then 1 else 0 end) as revisi,
	sum(case status when 6 then 1 else 0 end) as belumbayar,
	sum(case status when 7 then 1 else 0 end) as confirmed,
	sum(case when status = 8 then 1 else 0 end) as telahbayar,
	sum(case when status = 9 then 1 else 0 end) as siap,
	sum(case when status = 10 then 1 else 0 end) as delivered
from salestable
group by kodebc
GO
/****** Object:  View [dbo].[vw_salestable]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE                        view [dbo].[vw_salestable]
as
SELECT     T1.salesid, T1.orderdate, T1.kodemember, T1.namamember, T1.alamat, T1.telp, T1.email, T1.totalorder, T1.discount, T1.totalbayar, T1.paymentcharge, 
                      ISNULL(T4.totalorder, 0) AS totalorderbc, ISNULL(T4.discount, 0) AS discountbc, ISNULL(T4.totalbayar, 0) AS totalbayarbc, T2.kodebc, T2.namabc, 
                      T2.alamat AS alamatbc, T2.telp AS telpbc, T2.email AS emailbc, dbo.fn_salesuserstatus(T1.status) AS userstatus, T1.status, T1.paymentmode, 
                      ISNULL(T3.shortname, '') AS paymentname, T1.virtualaccount, CASE WHEN T1.status IN (2, 5, 6) AND T1.maxvalidatedate > getdate() 
                      THEN CASE WHEN datediff(mi, getdate(), T1.maxvalidatedate) >= 60 THEN CAST(datediff(mi, getdate(), T1.maxvalidatedate) / 60 AS varchar(3)) 
                      + ' Jam ' ELSE '' END + CAST(datediff(mi, getdate(), T1.maxvalidatedate) % 60 AS varchar(3)) + ' Menit' ELSE '0 Menit' END AS timeleft,
                          (SELECT     TOP 1 statusinfo
                            FROM          dbo.syncRequest
                            WHERE      (timestamp IN
                                                       (SELECT     MAX(timestamp) AS Expr1
                                                         FROM          dbo.syncRequest AS syncRequest_1
                                                         WHERE      (sessionid = T1.salesid)))) AS statusinfo, CASE WHEN DATEADD(dd, DATEDIFF(dd, 0, getdate()), 0) = DATEADD(dd, 
                      DATEDIFF(dd, 0, T1.MaxValidateDate), 0) THEN 0 ELSE 1 END AS validatesameday, T1.createddate, T1.maxpaiddate, T1.cancelcode, T1.validatedate, 
                      T1.paiddate, T1.canceldate, T1.deliverdate, T1.cleardate, T1.maxvalidatedate, CASE WHEN NOT T1.maxpaiddate IS NULL 
                      THEN CASE WHEN datediff(mi, getdate(), T1.maxpaiddate) >= 60 THEN CAST(datediff(mi, getdate(), T1.maxpaiddate) / 60 AS varchar(3)) 
                      + ' Jam ' ELSE '' END + CAST(datediff(mi, getdate(), T1.maxpaiddate) % 60 AS varchar(3)) + ' Menit' ELSE '0 Menit' END AS timeleftpaid, 
                      dbo.fn_bcsalesuserstatus(T1.status) AS bcsalesorderstatus
FROM         dbo.salesTable AS T1 WITH (nolock) LEFT OUTER JOIN
                      dbo.BCTable AS T2 WITH (nolock) ON T1.kodebc = T2.kodebc LEFT OUTER JOIN
                      dbo.paymentMode AS T3 WITH (nolock) ON T1.paymentmode = T3.paymentmode LEFT OUTER JOIN
                      dbo.purchTable AS T4 WITH (nolock) ON T1.salesid = T4.purchid
GO
/****** Object:  View [dbo].[vw_salesline]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE            view [dbo].[vw_salesline]
as
select 
	T1.salesid, T1.itemid, T2.itemname, T1.qty, T1.price, T1.pricembr,  
	T1.totalorder, T1.discount , T1.totalbayar,
	T1.qtyorder, T1.qtybc,isnull(T3.qty,0) as purchqty, 
	T1.Qty - (T1.QtyBC + isnull(T3.qty,0)) as shortageqty,
	isnull(T3.totalorder,0) as totalorderbc,
	isnull(T3.totalbayar,0) as totalbayarbc,
	(T1.QtyBC + isnull(T3.qty,0)) as qtyedited,
	(T1.qtybc+isnull(T3.qty,0))*T1.price as totalorderedited, 
	(T1.qtybc+isnull(T3.qty,0))*T1.pricembr - (T1.qtybc+isnull(T3.qty,0))*T1.price as discountedited, 
	(T1.qtybc+isnull(T3.qty,0))*T1.pricembr as totalbayaredited 
from salesLine T1 with (nolock)
inner join InventTable T2 with (nolock) on T1.itemid= T2.itemid
left join PurchLine T3 with (nolock) on T3.PurchId = T1.SalesID and T3.itemid = T1.itemid
GO
/****** Object:  View [dbo].[vw_report03]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    view [dbo].[vw_report03] as 
select 
	T2.salesid, T2.orderdate, T2.kodemember, T2.namamember,
	T2.paiddate,
	T2.totalbayar - T2.paymentcharge as totalbayarmbr, T2.paymentcharge, 
	T1.salesidsmi, T1.totalbayar as totalbayarbc, 
	T2.totalbayar - T2.paymentcharge - T1.totalbayar as kreditbc,
	dbo.fn_salesuserstatus(T2.status) as statusname,
	T1.kodebc, T2.status
from PurchTable T1
inner join SalesTable T2 on T1.PurchID = T2.SalesID
where T2.status in (8,9,10)
GO
/****** Object:  View [dbo].[vw_report02]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE  view [dbo].[vw_report02] as 
SELECT     T1.salesid, T1.orderdate, T1.kodemember, T1.namamember, T1.totalbayar, T1.paymentcharge, ISNULL(T2.salesidSMI, 'No Order') AS salesidsmi, 
                      dbo.fn_bcsalesuserstatus(T1.status) AS statusname, T1.kodebc, T1.status
FROM         dbo.salesTable AS T1 LEFT OUTER JOIN
                      dbo.purchTable AS T2 ON T1.salesid = T2.purchid
WHERE     (NOT (T1.orderdate IS NULL))
GO
/****** Object:  View [dbo].[vw_report01]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   view [dbo].[vw_report01] as 
SELECT     T1.itemid, T3.itemname, T1.qtybc, T1.salesid, T2.kodemember, T2.namamember, T2.kodebc, T2.status AS status, dbo.fn_bcsalesuserstatus(T2.status) 
                      AS statusname
FROM         dbo.salesLine AS T1 INNER JOIN
                      dbo.salesTable AS T2 ON T1.salesid = T2.salesid INNER JOIN
                      dbo.inventTable AS T3 ON T1.itemid = T3.itemid
WHERE     (T2.status IN (4, 5, 6, 7, 8, 9)) AND (T1.qtybc <> 0) OR
                      (T2.status = 0) AND (T1.qtybc <> 0) AND (T2.cleardate IS NULL)
GO
/****** Object:  View [dbo].[vw_purchtable]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    view [dbo].[vw_purchtable]
as
select T1.purchid, T1.salesidsmi, T1.orderdate, T1.totalorder, T1.discount, T1.totalbayar, isnull(T1.includeppn,0) as includeppn,
T2.kodebc, T2.namabc, T2.alamat as alamatbc, T2.telp as telpbc, T2.email as emailbc,
T1.status, 
case when isnull(T3.StatusInfo,'') != '' then dbo.fn_purchuserstatus(T1.status) + ' - ' + T3.StatusInfo
else dbo.fn_purchuserstatus(T1.status) 
end	as userstatus
from purchtable T1 with (nolock)
inner join BCTable T2 with (nolock) on T1.kodebc = T2.kodebc
left join SyncRequest T3 with (NOLOCK) on T1.PurchId = T3.SessionId and T3.requestid ='order'
GO
/****** Object:  View [dbo].[vw_purchline]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE       view [dbo].[vw_purchline]
as
select 
	T1.purchid, T1.itemid, T2.itemname, T1.qty, T1.price, T1.pricebc,  
	T1.totalorder, T1.discount , T1.totalbayar
from PurchLine T1 with (nolock)
left outer join InventTable T2 with (nolock) on T1.itemid= T2.itemid
GO
/****** Object:  View [dbo].[vw_onlineorder]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE           view [dbo].[vw_onlineorder]
as
select T1.salesid, T1.orderdate, T1.kodemember, T1.namamember, T1.kodebc, T1.totalorder, T1.discount, T1.totalbayar, 
case 
	when T1.status = 2 and T1.maxvalidatedate > getdate() then 
		case 
			when datediff(mi, getdate(), T1.maxvalidatedate) >= 60 then
				cast(datediff(mi, getdate(), T1.maxvalidatedate) / 60 as varchar(3)) + ' Jam '
			else ''
		end 
		+ cast(datediff(mi, getdate(), T1.maxvalidatedate) % 60 as varchar(3)) + ' Menit'
	else '0 Menit' 
end  as timeleft,
T1.status, dbo.fn_bcsalesuserstatus(T1.status) as userstatus, 
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
'' as syncstatus, 0 as syncstatuscode, T1.createddate
from salestable as T1 with (nolock)
where T1.status <> 1 -- Open Order
and T1.cleardate is null AND (T1.status < 10)
GO
/****** Object:  View [dbo].[vw_inventtable]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO
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
GO
/****** Object:  View [dbo].[vw_BCMapping]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE view [dbo].[vw_BCMapping]
as
select 
	T2.KodeMember, T1.kodebc, T1.kodebc + ' - ' + T1.namabc as label, 
	T1.namabc, T1.alamat, T1.telp, T1.email,T2.defaultbc
from BCTable T1 with(nolock)
inner join mappingTable T2 with(nolock) on T1.kodebc = T2.kodebc
where T1.suspend = 0
GO
/****** Object:  View [dbo].[vw_validateorderh]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE         view [dbo].[vw_validateorderh]
as
select 
	T1.salesid, T1.kodemember, T1.namamember, T1.orderDate, T1.totalbayar as totalbayarmember,
	dbo.fn_salesuserstatus(T1.status) as userstatus, T1.status,
	isnull(T2.totalbayar,0) as totalbayarbc, T1.kodebc
from SalesTable T1 with (nolock) 
left join PurchTable T2 with (nolock) on T1.SalesId = T2.PurchID
GO
/****** Object:  View [dbo].[vw_validateorderd]    Script Date: 06/08/2013 20:34:38 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
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

--select * from SalesLine
GO


/****** Object:  View [dbo].[vw_BcSalesCount]    Script Date: 06/17/2013 18:15:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE VIEW [dbo].[vw_BcSalesCount]
AS
SELECT T2.KodeBc, T3.NamaBc, CAST(YEAR(T2.OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(T2.OrderDate)), 2) AS Periode, 
	COUNT(*) AS TotalOrder
FROM dbo.MemberTable T1
	INNER JOIN dbo.salesTable T2 ON T2. KodeMember = T1.KodeMember
	LEFT JOIN dbo.BcTable T3 ON T3.KodeBc = T2.KodeBc
WHERE T2.Status IN (0, 10)
GROUP BY T2.KodeBc, T3.NamaBc,  CAST(YEAR(T2.OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(T2.OrderDate)), 2)
GO

/****** Object:  View [dbo].[vw_ListPerformanceOnlineOrderBc]    Script Date: 06/17/2013 18:15:24 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[vw_ListPerformanceOnlineOrderBc]
AS
SELECT 
	A.Periode, A.KodeBc, A.NamaBc,
	A.Success,
	A.NotSuccess,
	CASE WHEN A.Success + A.NotSuccess = 0 THEN 0 ELSE CAST(1 - A.AfterCutOff/(A.Success + A.NotSuccess) AS DECIMAL(19,2)) END AS BeforeCutOff,
	CASE WHEN A.Success + A.NotSuccess = 0 THEN 0 ELSE CAST(A.AfterCutOff/(A.Success + A.NotSuccess) AS DECIMAL(19,2)) END AS AfterCutOff,
	CASE WHEN A.Total = 0 THEN 0 ELSE A.OneDay / A.Total END AS OneDay, 
	CASE WHEN A.Total = 0 THEN 0 ELSE A.TwoDay / A.Total END AS TwoDay,
	CASE WHEN A.Total = 0 THEN 0 ELSE A.ThreeDay / A.Total END AS ThreeDay,
	CASE WHEN A.Total = 0 THEN 0 ELSE (A.Total - A.OneDay - A.TwoDay - A.ThreeDay)/ A.Total END AS MoreDay,
	A.PaidPlus3Day
FROM (
	SELECT 
		T1.Periode, T1.KodeBc, T2.NamaBc,
		dbo.fn_GetCountSuccessOrderBc(T1.KodeBc, T1.Periode) AS Success,
		dbo.fn_GetCountNotSuccessOrderBc(T1.KodeBc, T1.Periode) AS NotSuccess,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE bypasseddate IS NOT NULL AND KodeBc = T1.KodeBc AND YEAR(OrderDate) = LEFT(T1.Periode,4) AND MONTH(OrderDate) = RIGHT(T1.Periode,2) AND Status IN (0,8,9,10,11)) AS DECIMAL(19,2)) AfterCutOff,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE KodeBc = T1.KodeBc AND DeliverDate IS NOT NULL AND DATEDIFF(ss,OrderDate, DeliverDate) <= 86400) AS DECIMAL(19,2)) AS OneDay,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE KodeBc = T1.KodeBc AND DeliverDate IS NOT NULL AND DATEDIFF(ss,OrderDate, DeliverDate) > 86400 AND DATEDIFF(ss,OrderDate, DeliverDate) <= 172800) AS DECIMAL(19,2)) AS TwoDay,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE KodeBc = T1.KodeBc AND DeliverDate IS NOT NULL AND DATEDIFF(ss,OrderDate, DeliverDate) > 172800 AND DATEDIFF(ss,OrderDate, DeliverDate) <= 259200) AS DECIMAL(19,2)) AS ThreeDay,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE KodeBc = T1.KodeBc AND DeliverDate IS NOT NULL) AS DECIMAL(19,2)) AS Total,
		CAST((SELECT COUNT(*) FROM SalesTable WHERE KodeBc = T1.KodeBc AND status IN (8,9) AND DATEDIFF(ss,PaidDate, GETDATE()) > 172800) AS DECIMAL(19,2)) AS PaidPlus3Day
	FROM vw_BcSalesCount T1
		INNER JOIN BcTable T2 ON T1.KodeBc = T2.KodeBc
) A
GO



/****** Object:  View [dbo].[vw_MemberSalesCount]    Script Date: 06/17/2013 18:15:13 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[vw_MemberSalesCount]
AS
SELECT T1.KodeMember, T1.Name AS NamaMember, CAST(YEAR(T2.OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(T2.OrderDate)), 2) AS Periode, 
	COUNT(*) AS TotalOrder
FROM dbo.MemberTable T1
	INNER JOIN dbo.salesTable T2 ON T2. KodeMember = T1.KodeMember
WHERE T2.Status IN (0, 8, 9, 10, 11)
GROUP BY T1.KodeMember, T1.Name,  CAST(YEAR(T2.OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(T2.OrderDate)), 2)
GO

/****** Object:  View [dbo].[vw_MemberSuccessRate]    Script Date: 06/17/2013 18:15:09 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[vw_MemberSuccessRate]
AS
SELECT T1.KodeMember, T1.NamaMember, T1.Periode, T1.TotalOrder, 
	dbo.fn_GetCountSuccessOrder(T1.KodeMember, T1.Periode) AS SuccessOrder,
	CAST(CAST(dbo.fn_GetCountSuccessOrder(T1.KodeMember, T1.Periode) AS DECIMAL(19,4))/ CAST(T1.TotalOrder AS DECIMAL(19,4)) AS DECIMAL(19,2)) AS SuccessOrderPercent,
	dbo.fn_GetCountLatePayment(T1.KodeMember, T1.Periode) AS LatePayment,
	CAST(CAST(dbo.fn_GetCountLatePayment(T1.KodeMember, T1.Periode) AS DECIMAL(19,4))/ CAST(T1.TotalOrder AS DECIMAL(19,4)) AS DECIMAL(19,2)) AS LatePaymentPercent,
	dbo.fn_GetCountEmptyStock(T1.KodeMember, T1.Periode) AS EmptyStock, 
	CAST(CAST(dbo.fn_GetCountEmptyStock(T1.KodeMember, T1.Periode) AS DECIMAL(19,4))/ CAST(T1.TotalOrder AS DECIMAL(19,4)) AS DECIMAL(19,2)) AS EmptyStockPercent,
	dbo.fn_GetCountCancelByMember(T1.KodeMember, T1.Periode) + dbo.fn_GetCountRevisi(T1.KodeMember, T1.Periode) AS CancelByMember, 
	CAST(CAST(dbo.fn_GetCountCancelByMember(T1.KodeMember, T1.Periode) + dbo.fn_GetCountRevisi(T1.KodeMember, T1.Periode) AS DECIMAL(19,4))/ CAST(T1.TotalOrder AS DECIMAL(19,4))  AS DECIMAL(19,2)) AS CancelByMemberPercent,
	dbo.fn_GetCountTechnicalError(T1.KodeMember, T1.Periode) AS SystemFailure,
	CAST(CAST(dbo.fn_GetCountTechnicalError(T1.KodeMember, T1.Periode) AS DECIMAL(19,4))/ CAST(T1.TotalOrder AS DECIMAL(19,4)) AS DECIMAL(19,2)) AS SystemFailurePercent 
FROM vw_MemberSalesCount T1
GO

/****** Object:  View [dbo].[vw_sospaygate]    Script Date: 06/17/2013 18:15:04 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
/* =============================================
 ALTER  view basic template
 =============================================*/
CREATE VIEW [dbo].[vw_sospaygate]
AS
SELECT     dbo.salesTable.salesid, dbo.salesTable.kodemember, dbo.salesTable.namamember, dbo.salesTable.alamat, dbo.salesTable.telp, 
                      dbo.salesTable.email, dbo.salesTable.kodebc, dbo.salesTable.status, dbo.salesTable.edited, dbo.salesTable.totalbayar, dbo.salesTable.totalorder, 
                      dbo.salesTable.discount, dbo.salesTable.orderdate, dbo.salesTable.validatedate, dbo.salesTable.paymentcharge, dbo.paymentTable.paymentmode, 
                      dbo.paymentTable.trxref, dbo.paymentTable.trxdate, dbo.paymentTable.maxpaymdate, dbo.paymentTable.reference, dbo.paymentTable.paymstatus, 
                      dbo.paymentTable.initializedate, dbo.paymentTable.confirmeddate, CASE WHEN dbo.paymentTable.minpaiddate >= GETDATE() 
                      THEN DATEDIFF(SECOND, GETDATE(), dbo.paymentTable.minpaiddate) ELSE 0 END AS paymtimemin, 
                      CASE WHEN dbo.paymentTable.maxpaiddate >= GETDATE() THEN DATEDIFF(SECOND, GETDATE(), dbo.paymentTable.maxpaiddate) 
                      ELSE 0 END AS paymtimeleft, dbo.salesTable.paiddate, dbo.purchTable.salesidSMI, dbo.purchTable.totalbayar AS purchtotalbayar, 
                      dbo.purchTable.status AS purchstatus, GETDATE() AS currentDate, dbo.salesTable.cancelcode, dbo.paymentMode.gateway
FROM         dbo.salesTable LEFT OUTER JOIN
                      dbo.purchTable ON dbo.salesTable.salesid = dbo.purchTable.purchid LEFT OUTER JOIN
                      dbo.paymentTable ON dbo.salesTable.salesid = dbo.paymentTable.salesid LEFT OUTER JOIN
                      dbo.paymentMode ON dbo.paymentTable.paymentmode = dbo.paymentMode.paymentmode
GO
