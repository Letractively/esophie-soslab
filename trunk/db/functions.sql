USE [SOS2]
GO
/****** Object:  UserDefinedFunction [dbo].[fn_salesuserstatus]    Script Date: 05/03/2013 23:58:44 ******/
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
		when 2 then 'On Order'
		when 3 then 'Dikirim ke BC'
		when 4 then 'Dikirim ke BC'
		when 5 then 'Revisi'
		when 6 then 'Validasi BC'
		when 7 then 'Sudah Bayar'
		when 8 then 'Sudah Bayar'
		when 9 then 'Siap diambil'
		when 10 then 'Delivered'
		when 11 then 'Clear'
	end 
return(@ret)
end
GO
/****** Object:  UserDefinedFunction [dbo].[fn_purchuserstatus]    Script Date: 05/03/2013 23:58:44 ******/
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
/****** Object:  UserDefinedFunction [dbo].[fn_bcsalesuserstatus]    Script Date: 05/03/2013 23:58:44 ******/
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
/****** Object:  UserDefinedFunction [dbo].[Date2UnixTimeStamp]    Script Date: 05/03/2013 23:58:44 ******/
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
/****** Object:  UserDefinedFunction [dbo].[fn_xmlspecialchars]    Script Date: 05/03/2013 23:58:44 ******/
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
/****** Object:  UserDefinedFunction [dbo].[fn_sos_onhandratio]    Script Date: 05/03/2013 23:58:44 ******/
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
/****** Object:  UserDefinedFunction [dbo].[fn_setemailmsg]    Script Date: 05/03/2013 23:58:44 ******/
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
