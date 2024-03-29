
/****** Object:  UserDefinedFunction [dbo].[fn_salesuserstatus]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE          function [dbo].[fn_salesuserstatus]
(@status smallint)
returns varchar(20)
as
begin
	
declare @ret varchar(20)
set @ret = 
	case @status
		when 0 then 'Batal'
		when 1 then 'Order Baru'
		when 2 then 'Dikirim ke BC'
		when 3 then 'Dikirim ke BC'
		when 4 then 'Dikirim ke BC'
		when 5 then 'Revisi'
		when 6 then 'Belum Bayar'
		when 7 then 'Sudah Bayar'
		when 8 then 'Persiapan'
		when 9 then 'Siap diambil'
		when 10 then 'Delivered'
		when 11 then 'Clear'
	end 
return(@ret)
end
GO
/****** Object:  UserDefinedFunction [dbo].[fn_purchuserstatus]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    function [dbo].[fn_purchuserstatus]
(@status smallint)
returns varchar(20)
as
begin
	
declare @ret varchar(20)
set @ret = 
	case @status
		when 0 then 'Cancelled'
		when 1 then 'New Order'
		when 2 then 'In Progress'
		when 3 then 'On Order'
		when 9 then 'Delivered'
		when 10 then 'Clear'
	end 
return(@ret)
end
GO
/****** Object:  UserDefinedFunction [dbo].[fn_bcsalesuserstatus]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create          function [dbo].[fn_bcsalesuserstatus]
(@status smallint)
returns varchar(20)
as
begin
	
declare @ret varchar(20)
set @ret = 
	case @status
		when 0 then 'Cancelled'
		when 1 then 'Open Order'
		when 2 then 'New Order'
		when 3 then 'In Progress'
		when 4 then 'In Progress'
		when 5 then 'Edited'
		when 6 then 'Waiting payment'
		when 7 then 'Payment confirmed'
		when 8 then 'Paid'
		when 9 then 'Ready for pickup'
		when 10 then 'Delivered'
		when 11 then 'Clear'
	end 
return(@ret)
end
GO
/****** Object:  UserDefinedFunction [dbo].[Date2UnixTimeStamp]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
/*
simple:
SELECT DATEDIFF(s, '19700101', GETDATE()) 
 
Now, there is one caveat with this method. 
It will only work on dates prior to 2038-01-19 at 3:14:08 AM, 
where the delta in seconds exceeds the limit of the INT datatype 
(INT is used as the result of DATEDIFF). 
If you want your code to handle dates beyond 2038-01-18, 
and you are running SQL Server 2000, you can write a user-defined function: 
*/

CREATE FUNCTION [dbo].[Date2UnixTimeStamp]
( 
    @dt DATETIME 
) 
RETURNS BIGINT 
AS 
BEGIN 
    DECLARE @diff BIGINT 
    IF @dt >= '20380119' 
    BEGIN 
        SET @diff = CONVERT(BIGINT, DATEDIFF(S, '19700101', '20380119')) 
            + CONVERT(BIGINT, DATEDIFF(S, '20380119', @dt)) 
    END 
    ELSE 
        SET @diff = DATEDIFF(S, '19700101', @dt) 
    RETURN @diff 
END
GO
/****** Object:  UserDefinedFunction [dbo].[fn_xmlspecialchars]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_xmlspecialchars]
(
	-- Add the parameters for the function here
	@text nvarchar(2000)
)
RETURNS nvarchar(2000)
AS
BEGIN
	DECLARE @escapetxt nvarchar(2000)
	-- Return the result of the function
	SELECT @escapetxt = REPLACE(REPLACE(REPLACE(@text, CHAR(13), CHAR(32)), CHAR(10), CHAR(32)), CHAR(9), CHAR(32))
	RETURN @escapetxt

END
GO
/****** Object:  UserDefinedFunction [dbo].[fn_sos_onhandratio]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE    FUNCTION [dbo].[fn_sos_onhandratio] (@onhand numeric, @ratio smallint, @min numeric)
RETURNS numeric AS 
BEGIN 
	DECLARE @onhandratio numeric, @onhandmin numeric, @onhandsos numeric
	SET @onhandratio = FLOOR(@onhand * @ratio / 100)
	SET @onhandmin =  FLOOR(@onhand - @min)
	SET @onhandsos = 0
	IF (@onhandratio < 0 )  SET @onhandratio = 0
	IF (@onhandmin < 0 )  SET @onhandmin = 0
	IF (@onhandratio > @onhandmin) SET @onhandsos = @onhandmin
	ELSE SET @onhandsos = @onhandratio
	RETURN @onhandsos
END
GO
/****** Object:  UserDefinedFunction [dbo].[fn_setemailmsg]    Script Date: 06/08/2013 20:36:25 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE  function [dbo].[fn_setemailmsg]
(
	@msg 		varchar(500),
	@salesid	varchar(20),
	@mbrcode  	varchar(10),
	@mbrname	varchar(50),
	@bccode  	varchar(10),
	@bcname		varchar(50)
)
returns varchar(500)
as
begin
	
declare 
	@ret 		varchar(500),
	@bcurl 		varchar(80),
	@mbrurl 	varchar(80)

select 
	@bcurl = bcurl,
	@mbrurl = mbrurl
from sysparamtable with (nolock)

set @ret = @msg
set @ret = replace(@ret,'[orderno]',@salesid)
set @ret = replace(@ret,'[mbrno]',@mbrcode)
set @ret = replace(@ret,'[mbrname]',@mbrname)
set @ret = replace(@ret,'[bcno]',@bccode)
set @ret = replace(@ret,'[bcname]',@bcname)
set @ret = replace(@ret,'[bcurl]',@bcurl)
set @ret = replace(@ret,'[mbrurl]',@mbrurl)

return(@ret)
end
GO



/****** Object:  UserDefinedFunction [dbo].[fn_GetCountCancelByMember]    Script Date: 06/13/2013 16:39:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountCancelByMember]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode = 1
		AND Status = 0
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

/****** Object:  UserDefinedFunction [dbo].[fn_GetCountEmptyStock]    Script Date: 06/13/2013 16:40:00 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountEmptyStock]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode = 3
		AND Status = 0
		
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

/****** Object:  UserDefinedFunction [dbo].[fn_GetCountLatePayment]    Script Date: 06/13/2013 16:40:13 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountLatePayment]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode = 2
		AND Status = 0
	-- Return the result of the function
	RETURN @ReturnValue

END
GO


/****** Object:  UserDefinedFunction [dbo].[fn_GetCountNotSuccessOrderBc]    Script Date: 06/13/2013 16:40:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountNotSuccessOrderBc]
(
	-- Add the parameters for the function here
	@KodeBc VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeBc = @KodeBc
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode IN (0, 1, 2, 3, 4)
		AND Status = 0
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

/****** Object:  UserDefinedFunction [dbo].[fn_GetCountRevisi]    Script Date: 06/13/2013 16:41:00 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountRevisi]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode = 4
		AND Status = 0
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

/****** Object:  UserDefinedFunction [dbo].[fn_GetCountSuccessOrder]    Script Date: 06/13/2013 16:41:12 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountSuccessOrder]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND Status >= 8
		
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

/****** Object:  UserDefinedFunction [dbo].[fn_GetCountTechnicalError]    Script Date: 06/13/2013 16:41:32 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountTechnicalError]
(
	-- Add the parameters for the function here
	@KodeMember VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeMember = @KodeMember
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND CancelCode = 0
		AND Status = 0
	-- Return the result of the function
	RETURN @ReturnValue

END
GO


/****** Object:  UserDefinedFunction [dbo].[fn_GetCountSuccessOrderBc]    Script Date: 06/13/2013 16:41:26 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date, ,>
-- Description:	<Description, ,>
-- =============================================
CREATE FUNCTION [dbo].[fn_GetCountSuccessOrderBc]
(
	-- Add the parameters for the function here
	@KodeBc VARCHAR(50),
	@YearMonth VARCHAR(6)
)
RETURNS INT
AS
BEGIN
	-- Declare the return variable here
	DECLARE @ReturnValue INT
	DECLARE @TSQL VARCHAR(5000)
	-- Add the T-SQL statements to compute the return value here
	SELECT @ReturnValue = COUNT(*)
	FROM dbo.SalesTable
	WHERE KodeBc = @KodeBc
		AND CAST(YEAR(OrderDate) AS VARCHAR(4))+ RIGHT('0' + RTRIM(MONTH(OrderDate)), 2) = @YearMonth
		AND Status >= 8
		
	-- Return the result of the function
	RETURN @ReturnValue

END
GO

-- ================================================
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE Function [fn_RemoveNonNumericCharacters](@strText VARCHAR(1000))
RETURNS VARCHAR(1000)
AS
BEGIN
    WHILE PATINDEX('%[^0-9]%', @strText) > 0
    BEGIN
        SET @strText = STUFF(@strText, PATINDEX('%[^0-9]%', @strText), 1, '')
    END
    RETURN @strText
END
GO