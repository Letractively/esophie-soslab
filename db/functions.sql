SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[fn_sos_onhandratio]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'
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
' 
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[fn_salesuserstatus]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'
CREATE    function [dbo].[fn_salesuserstatus]
(@status smallint)
returns varchar(20)
as
begin
	
declare @ret varchar(20)
set @ret = 
	case @status
		when 0 then ''Cancelled''
		when 1 then ''Belum Bayar''
		when 2 then ''Dikirim ke BC''
		when 3 then ''In Proses''
		when 4 then ''In Proses''
		when 5 then ''Edited''
		when 6 then ''Belum Bayar''
		when 7 then ''Sudah Bayar''
		when 8 then ''Sudah Bayar''
		when 9 then ''Delivered''
		when 10 then ''Clear''
	end 
return(@ret)
end
' 
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[fn_setemailmsg]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'
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
set @ret = replace(@ret,''[orderno]'',@salesid)
set @ret = replace(@ret,''[mbrno]'',@mbrcode)
set @ret = replace(@ret,''[mbrname]'',@mbrname)
set @ret = replace(@ret,''[bcno]'',@bccode)
set @ret = replace(@ret,''[bcname]'',@bcname)
set @ret = replace(@ret,''[bcurl]'',@bcurl)
set @ret = replace(@ret,''[mbrurl]'',@mbrurl)

return(@ret)
end
' 
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[Date2UnixTimeStamp]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'/*
simple:
SELECT DATEDIFF(s, ''19700101'', GETDATE()) 
 
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
    IF @dt >= ''20380119'' 
    BEGIN 
        SET @diff = CONVERT(BIGINT, DATEDIFF(S, ''19700101'', ''20380119'')) 
            + CONVERT(BIGINT, DATEDIFF(S, ''20380119'', @dt)) 
    END 
    ELSE 
        SET @diff = DATEDIFF(S, ''19700101'', @dt) 
    RETURN @diff 
END
' 
END
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[fn_xmlspecialchars]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'

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
' 
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[fn_purchuserstatus]') AND xtype in (N'FN', N'IF', N'TF'))
BEGIN
execute dbo.sp_executesql @statement = N'
CREATE   function [dbo].[fn_purchuserstatus]
(@status smallint)
returns varchar(20)
as
begin
	
declare @ret varchar(20)
set @ret = 
	case @status
		when 0 then ''Cancelled''
		when 1 then ''Open Order''
		when 2 then ''Ordered''
		when 3 then ''On Order''
		when 9 then ''Delivered''
		when 10 then ''Clear''
	end 
return(@ret)
end
' 
END

