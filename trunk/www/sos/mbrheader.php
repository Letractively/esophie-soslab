<? header('Content-Type: text/html; charset=utf-8');
include_once "mbrcontroller.php";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ind" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Sophie Mobile - Online Orders</title>
	<meta name="viewport" content="width=360"/> 
	<link rel="icon" type="image/ico" href="images/favicon.ico"/>
        <link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon-precomposed.png" />
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
	<link type="text/css" rel="stylesheet" href="css/globalmobile.css"/>
        <? include_once "mbranalytics.php"; ?> 
        <script type="text/javascript" src="script/global.js?v3"></script>
	</head>
	<body class="mobile">     
	<form id="frmexec" method="post" accept-charset="utf-8"></form>
	<form id="frmmain" action="<?=$ctrl->filename()?>" method="post" accept-charset="utf-8">
		<div class="imglogo"><a href="mbrviewhistory.php">
                    <img src="images/logo.png" class="logo" alt="Sophie Online Shopping"/></a>
                </div>
		<div class="boxmain" style="width:340px;">
		<input type="hidden" id="pageaction" name="pageaction"/>
		<input type="hidden" id="salesid" name="salesid" value="<?=$ctrl->value("salesid")?>"/>