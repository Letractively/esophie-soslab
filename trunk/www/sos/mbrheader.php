<?include_once "mbrcontroller.php";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ind" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<title>Sophie Online Shopping</title>
	<meta name="viewport" content="width=360;"/> 
	<link rel="icon" type="image/ico" href="images/favicon.ico"/>
	<link type="text/css" rel="stylesheet" href="css/globalmobile.css"/>
	<script language="Javascript" type="text/javascript" src="script/global.js"/>
	</head>
	<body class="mobile">
	<form id="frmexec" method="post"></form>
	<form id="frmmain" action="<?=$ctrl->filename()?>" method="post">
		<center>
                <a href="mbrviewhistory.php"><img src="images/logo.png" class="logo" alt="Sophie Online Shopping"/></a>
		<div class="boxmain" style="width:340px;">
		<input type="hidden" id="pageaction" name="pageaction"/>
		<input type="hidden" id="salesid" name="salesid" value="<?=$ctrl->value("salesid")?>"/>