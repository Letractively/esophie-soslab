SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[inventTableMaster]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[inventTableMaster](
	[itemId] [varchar](20) NOT NULL,
	[itemName] [varchar](30) NULL,
	[nameAlias] [varchar](30) NULL,
	[dimension] [varchar](10) NULL,
	[brand] [varchar](20) NULL,
	[itemGroup] [varchar](60) NULL,
	[itemSubGroup] [varchar](60) NULL,
	[fabric] [varchar](60) NULL,
	[gender] [varchar](10) NULL,
	[description] [varchar](400) NULL,
	[color] [varchar](30) NULL,
	[material] [varchar](30) NULL,
	[dimensions] [varchar](30) NULL,
	[bodyshapes] [varchar](20) NULL,
	[description_ID] [varchar](400) NULL,
	[status_ID] [varchar](10) NULL,
	[catalog_ID] [varchar](10) NULL,
	[catalogNo_ID] [varchar](20) NULL,
	[priceCTL_ID] [numeric](28, 12) NULL,
	[priceMBR_ID] [numeric](28, 12) NULL,
	[priceBC_ID] [numeric](28, 12) NULL,
	[blocked_ID] [tinyint] NULL CONSTRAINT [DF_inventTableMaster_blocked_ID]  DEFAULT (0),
	[dateUpdated] [datetime] NULL,
	[dateCreated] [datetime] NULL,
 CONSTRAINT [PK_InventTableMaster] PRIMARY KEY CLUSTERED 
(
	[itemId] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[mappingTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[mappingTable](
	[kodemember] [varchar](10) NOT NULL,
	[kodebc] [varchar](10) NOT NULL,
	[defaultbc] [smallint] DEFAULT (0),
	[lastf1date] [datetime] NULL,
 CONSTRAINT [PK_mappingTable] PRIMARY KEY CLUSTERED 
(
	[kodemember] ASC,
	[kodebc] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[memberTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[memberTable](
	[kodemember] [varchar](50) NULL,
	[acceptdate] [datetime] NULL,
	[suspend] [smallint] NULL CONSTRAINT [DF_memberTable_suspend]  DEFAULT (0),
	[name] [varchar](50) NULL,
	[address] [varchar](250) NULL,
	[phone] [varchar](20) NULL,
	[email] [varchar](80) NULL
) ON [PRIMARY]
END
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[parameterTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[parameterTable](
	[paramcode] [varchar](20) NOT NULL,
	[paramcol] [varchar](20) NULL,
	[valueStr] [varchar](200) NULL,
	[valueStr2] [varchar](8000) NULL,
	[valueInt] [int] NULL,
	[Description] [varchar](200) NULL,
 CONSTRAINT [PK_parameterTable] PRIMARY KEY CLUSTERED 
(
	[paramcode] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[paymentMode]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[paymentMode](
	[paymentmode] [varchar](10) NOT NULL,
	[name] [varchar](50) NULL,
	[shortname] [varchar](50) NULL,
	[active] [smallint] NULL,
	[chargeratio] [decimal](18, 0) NULL,
	[chargefee] [decimal](18, 0) NULL,
	[safetydelay] [int] NULL,
	[Description] [varchar](200) NULL,
	[seqno] [smallint] NULL,
	[merchantid] [varchar](30) NULL,
	[currencycode] [varchar](3) NULL,
	[paymentto] [varchar](100) NULL,
	[returnurl] [varchar](100) NULL,
	[password] [varchar](50) NULL,
	[inputMobileNumber] [smallint] NULL,
	[paymtimeout] [int] NULL,
	[initiatetimeout] [int] NULL,
	[reconciletimeout] [int] NULL,
 CONSTRAINT [PK_paymentMode] PRIMARY KEY CLUSTERED 
(
	[paymentmode] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[paymentTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[paymentTable](
	[salesid] [varchar](20) NOT NULL,
	[paymstatus] [smallint] NOT NULL CONSTRAINT [DF_paymentTable_paymstatus]  DEFAULT (0),
	[paymentmode] [varchar](20) NULL,
	[virtualaccount] [varchar](20) NULL,
	[msisdn] [varchar](20) NULL,
	[reference] [varchar](20) NULL,
	[trxref] [varchar](20) NULL,
	[trxdate] [varchar](20) NULL,
	[startdate] [datetime] NULL,
	[initializedate] [datetime] NULL,
	[confirmeddate] [datetime] NULL,
	[reconcileddate] [datetime] NULL,
	[maxpaymdate] [datetime] NULL,
	[minpaiddate] [datetime] NULL,
	[maxpaiddate] [datetime] NULL
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[purchLine]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[purchLine](
	[purchid] [varchar](20) NOT NULL,
	[itemid] [char](10) NOT NULL,
	[qty] [decimal](18, 0) NULL,
	[price] [decimal](18, 0) NULL,
	[priceBC] [decimal](18, 0) NULL,
	[totalorder] [decimal](18, 0) NULL,
	[discount] [decimal](18, 0) NULL,
	[totalbayar] [decimal](18, 0) NULL,
	[rowid] [smallint] NULL,
	[responsecode] [varchar](50) NULL,
 CONSTRAINT [PK_purchLine] PRIMARY KEY CLUSTERED 
(
	[purchid] ASC,
	[itemid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[purchTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[purchTable](
	[purchid] [varchar](20) NOT NULL,
	[salesidSMI] [varchar](20) NULL,
	[kodebc] [char](10) NULL,
	[orderdate] [datetime] NULL,
	[totalorder] [decimal](18, 0) NULL CONSTRAINT [DF_purchTable_totalorder]  DEFAULT (0),
	[discount] [decimal](18, 0) NULL CONSTRAINT [DF_purchTable_discount]  DEFAULT (0),
	[includeppn] [decimal](18, 0) NULL CONSTRAINT [DF_purchTable_includeppn]  DEFAULT (0),
	[totalbayar] [decimal](18, 0) NULL CONSTRAINT [DF_purchTable_totalbayar]  DEFAULT (0),
	[status] [smallint] NULL,
	[bypasseddate] [datetime] NULL,
	[acceptdate] [datetime] NULL,
	[deliverdate] [datetime] NULL,
	[cleardate] [datetime] NULL,
	[canceldate] [datetime] NULL,
	[synctimestamp] [bigint] NULL,
	[responsecode] [varchar](50) NULL,
 CONSTRAINT [PK_purchTable] PRIMARY KEY CLUSTERED 
(
	[purchid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[salesLine]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[salesLine](
	[salesid] [varchar](20) NOT NULL,
	[itemid] [varchar](10) NOT NULL,
	[qty] [decimal](18, 0) NULL,
	[qtyorder] [decimal](18, 0) NULL,
	[qtybc] [decimal](18, 0) NULL CONSTRAINT [DF_salesLine_qtybc]  DEFAULT (0),
	[price] [decimal](18, 0) NULL,
	[pricembr] [decimal](18, 0) NULL,
	[totalorder] [decimal](18, 0) NULL,
	[discount] [decimal](18, 0) NULL,
	[totalbayar] [decimal](18, 0) NULL,
	[rowid] [smallint] NULL,
 CONSTRAINT [PK_salesLine] PRIMARY KEY CLUSTERED 
(
	[salesid] ASC,
	[itemid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[salesTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[salesTable](
	[salesid] [varchar](20) NOT NULL,
	[kodemember] [varchar](10) NULL,
	[namamember] [varchar](50) NULL,
	[alamat] [varchar](200) NULL,
	[telp] [varchar](50) NULL,
	[email] [varchar](80) NULL,
	[kodebc] [varchar](10) NULL,
	[status] [smallint] NULL,
	[totalorder] [decimal](18, 0) NULL CONSTRAINT [DF_salesTable_totalorder]  DEFAULT (0),
	[discount] [decimal](18, 0) NULL CONSTRAINT [DF_salesTable_total]  DEFAULT (0),
	[totalbayar] [decimal](18, 0) NULL CONSTRAINT [DF_salesTable_totaldisc]  DEFAULT (0),
	[paymentcharge] [decimal](18, 0) NULL CONSTRAINT [DF_salesTable_paymentcharge]  DEFAULT (0),
	[paymentmode] [varchar](10) NULL,
	[virtualaccount] [varchar](20) NULL,
	[orderdate] [datetime] NULL,
	[editeddate] [datetime] NULL,
	[validatedate] [datetime] NULL,
	[maxvalidatedate] [datetime] NULL,
	[bypasseddate] [datetime] NULL,
	[confirmeddate] [datetime] NULL,
	[paiddate] [datetime] NULL,
	[maxpaiddate] [datetime] NULL,
	[deliverdate] [datetime] NULL,
	[cleardate] [datetime] NULL,
	[canceldate] [datetime] NULL,
	[cancelcode] [smallint] NULL,
	[paymentMobileNumber] [varchar](30) NULL,
	[edited] [smallint] NULL,
 CONSTRAINT [PK_trOrders] PRIMARY KEY CLUSTERED 
(
	[salesid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END
GO

IF NOT EXISTS (SELECT * FROM dbo.sysindexes WHERE id = OBJECT_ID(N'[dbo].[salesTable]') AND name = N'status_idx')
CREATE NONCLUSTERED INDEX [status_idx] ON [dbo].[salesTable] 
(
	[status] ASC
) ON [PRIMARY]

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[smsTemplate]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[smsTemplate](
	[smscode] [varchar](20) NOT NULL,
	[smsmsg] [varchar](500) NULL,
 CONSTRAINT [PK_smsTemplate] PRIMARY KEY CLUSTERED 
(
	[smscode] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[sysparamTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[sysparamTable](
	[bcstartwork] [int] NULL,
	[bcendwork] [int] NULL,
	[bcvalidate] [int] NULL,
	[maxtotalsales] [decimal](18, 0) NULL,
	[bcincludeppn] [decimal](18, 0) NULL,
	[bcurl] [varchar](80) NULL,
	[mbrurl] [varchar](80) NULL,
	[emailfrom] [varchar](80) NULL
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[sequenceTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[sequenceTable](
	[category] [varchar](20) NULL,
	[tahun] [int] NULL,
	[bulan] [int] NULL,
	[nextno] [int] NULL,
	[format] [varchar](20) NULL
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[smsTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[smsTable](
	[noseq] [int] IDENTITY(1,1) NOT NULL,
	[phone] [varchar](20) NULL,
	[message] [varchar](160) NULL,
	[createdDate] [datetime] NULL,
	[sendDate] [datetime] NULL,
	[messageid] [varchar](50) NULL,
 CONSTRAINT [PK_smsTable] PRIMARY KEY CLUSTERED 
(
	[noseq] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[syncOrderLine]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[syncOrderLine](
	[timestamp] [bigint] NOT NULL,
	[sessionid] [varchar](50) NOT NULL,
	[itemid] [varchar](10) NOT NULL,
	[rowid] [smallint] NULL,
	[price] [decimal](18, 0) NULL,
	[qtyorder] [decimal](18, 0) NULL,
	[qtyimport] [decimal](18, 0) NULL,
	[responsecode] [varchar](50) NULL,
 CONSTRAINT [PK_syncOrderLine] PRIMARY KEY CLUSTERED 
(
	[timestamp] ASC,
	[sessionid] ASC,
	[itemid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[syncOrderTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[syncOrderTable](
	[timestamp] [bigint] NOT NULL,
	[sessionid] [varchar](50) NOT NULL,
	[responsecode] [varchar](50) NULL,
	[kodebc] [varchar](10) NOT NULL,
	[kodemember] [varchar](10) NOT NULL,
	[deliveryaddress] [varchar](250) NULL,
	[deliveryphone] [varchar](50) NULL,
	[deliveryemail] [varchar](50) NULL,
	[salesid] [varchar](20) NULL,
	[total] [decimal](18, 0) NULL,
	[force] [tinyint] NULL,
 CONSTRAINT [PK_syncOrderTable] PRIMARY KEY CLUSTERED 
(
	[timestamp] ASC,
	[sessionid] ASC,
	[kodebc] ASC,
	[kodemember] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[syncRequest]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[syncRequest](
	[timestamp] [bigint] NOT NULL,
	[sessionid] [varchar](50) NOT NULL,
	[requestid] [varchar](10) NOT NULL,
	[status] [smallint] NULL,
	[axprocess] [varchar](5) NULL,
	[createddate] [datetime] NULL CONSTRAINT [DF_syncRequest_createddate]  DEFAULT (getdate()),
	[startdate] [datetime] NULL,
	[enddate] [datetime] NULL,
	[responsecode] [varchar](50) NULL,
	[statusinfo] [varchar](50) NULL,
	[retrynumber] [smallint] NULL CONSTRAINT [DF_syncRequest_retrynumber]  DEFAULT (0),
 CONSTRAINT [PK_syncRequest] PRIMARY KEY CLUSTERED 
(
	[timestamp] ASC,
	[sessionid] ASC,
	[requestid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[BCTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[BCTable](
	[kodebc] [varchar](10) NOT NULL,
	[namabc] [varchar](50) NULL,
	[password] [varchar](32) NULL,
	[alamat] [varchar](200) NULL,
	[telp] [varchar](50) NULL,
	[email] [varchar](80) NULL,
	[suspend] [smallint] NULL CONSTRAINT [DF_BCTable_suspend]  DEFAULT (0),
 CONSTRAINT [PK_BCTable] PRIMARY KEY CLUSTERED 
(
	[kodebc] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[emailTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[emailTable](
	[noseq] [int] IDENTITY(1,1) NOT NULL,
	[from] [varchar](100) NULL,
	[to] [varchar](100) NULL,
	[cc] [varchar](100) NULL,
	[bcc] [varchar](100) NULL,
	[subject] [varchar](100) NULL,
	[body] [varchar](800) NULL,
	[createdDate] [datetime] NULL,
	[sendDate] [datetime] NULL,
	[toname] [varchar](50) NULL,
 CONSTRAINT [PK_emailTable] PRIMARY KEY CLUSTERED 
(
	[noseq] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[emailTemplate]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[emailTemplate](
	[emailcode] [varchar](20) NOT NULL,
	[description] [varchar](80) NULL,
	[subject] [varchar](80) NULL,
	[body] [varchar](500) NULL,
 CONSTRAINT [PK_emailTemplate] PRIMARY KEY CLUSTERED 
(
	[emailcode] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[inventGroup]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[inventGroup](
	[inventGroupId] [varchar](10) NOT NULL,
	[onHandMin] [numeric](10, 0) NOT NULL CONSTRAINT [DF_InventGroup_OnHandMin]  DEFAULT (0),
	[onHandRatio] [smallint] NOT NULL CONSTRAINT [DF_InventGroup_OnHandRatio]  DEFAULT (100),
 CONSTRAINT [PK_InventGroup] PRIMARY KEY CLUSTERED 
(
	[inventGroupId] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[inventMappingBrand]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[inventMappingBrand](
	[NSUSizeGroupId] [varchar](20) NOT NULL,
	[brand] [varchar](20) NULL,
 CONSTRAINT [PK_InventMappingBrand] PRIMARY KEY CLUSTERED 
(
	[NSUSizeGroupId] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[inventSum]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[inventSum](
	[itemId] [varchar](20) NOT NULL,
	[qtyOnHandAx] [numeric](18, 0) NULL,
	[qtyOnHandSOS] [numeric](18, 0) NULL,
	[dateUpdated] [datetime] NULL,
 CONSTRAINT [PK_InventSum] PRIMARY KEY CLUSTERED 
(
	[itemId] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



CREATE    TRIGGER [TRIG_INVENTSUM_UPDATED]
ON [dbo].[inventSum]
FOR UPDATE 
AS

-- STOP IF 0 ROWS UPDATED
IF @@ROWCOUNT = 0 RETURN

IF UPDATE (qtyOnHandAx)
BEGIN
	SET NOCOUNT ON

	-- UPDATE qtyOnHandSOS
	UPDATE INVENTSUM SET
	 	qtyOnHandSOS = dbo.fn_sos_onhandratio(Inserted.qtyOnHandAx, INVENTGROUP.OnHandRatio, INVENTGROUP.OnHandMin)
	FROM INVENTSUM
	INNER JOIN Inserted 
		ON Inserted.itemId = INVENTSUM.itemId
	INNER JOIN  INVENTTABLE WITH (NOLOCK)
		ON INVENTTABLE.ITEMID = Inserted.itemId
	INNER JOIN  INVENTGROUP WITH (NOLOCK)
		ON INVENTGROUP.INVENTGROUPID = INVENTTABLE.INVENTGROUPID
	WHERE 	INVENTSUM.qtyOnHandSOS IS NULL OR
		INVENTSUM.qtyOnHandSOS <> dbo.fn_sos_onhandratio(Inserted.qtyOnHandAx, INVENTGROUP.OnHandRatio, INVENTGROUP.OnHandMin)

	-- CASCADE TO INVENTTABLE
	UPDATE INVENTTABLE SET
		qty = INVENTSUM.qtyOnHandSOS,
		updatedate = GETDATE()
	FROM INVENTTABLE 
	INNER JOIN INVENTSUM 
		ON INVENTSUM.itemId = INVENTTABLE.itemId
	INNER JOIN Inserted 
		ON Inserted.itemId = INVENTSUM.itemId
	WHERE 	INVENTTABLE.qty IS NULL OR
		INVENTTABLE.qty <> INVENTSUM.qtyOnHandSOS

	SET NOCOUNT OFF
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[inventTable]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
BEGIN
CREATE TABLE [dbo].[inventTable](
	[itemid] [varchar](50) NOT NULL,
	[itemname] [varchar](50) NULL,
	[qty] [decimal](18, 0) NULL,
	[pricecatalog] [decimal](18, 0) NULL,
	[priceBC] [decimal](18, 0) NULL,
	[priceMBR] [decimal](18, 0) NULL,
	[deadstyle] [smallint] NULL CONSTRAINT [DF_inventTable_deadstyle]  DEFAULT (0),
	[createdate] [datetime] NULL CONSTRAINT [DF_inventTable_createdate]  DEFAULT (getdate()),
	[updatedate] [datetime] NULL,
	[inventGroupId] [varchar](10) NULL,
 CONSTRAINT [PK_inventtable] PRIMARY KEY CLUSTERED 
(
	[itemid] ASC
) ON [PRIMARY]
) ON [PRIMARY]
END
