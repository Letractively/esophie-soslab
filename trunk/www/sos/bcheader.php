<?include_once "bccontroller.php";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Sophie Online Shopping</title>
	<meta name="viewport" content="width=360;"> 
	<link rel="icon" type="image/ico" href="images/favicon.ico">
	<link type="text/css" rel="stylesheet" href="css/global.css">
	<link type="text/css" rel="stylesheet" href="css/calendar.css">
	<script language="javascript" src="script/global.js"></script>
	<script language="javascript" src="script/calendar.js"></script>
	</head>
	<body>
	<form id="frmmain" name="frmmain" action="<?=$ctrl->filename()?>" method="post">
		<center>		
		<img src="images/logo.png" class="logo" alt="Sophie Online Shopping"/>
		<input type="hidden" id="pageaction" name="pageaction">		
		<div name="MyCalendar" id="MyCalendar"></div>	
		<?include_once "bcmenu.php";?>
		<div class="boxmain" <? if ($ctrl->filename() == 'bclogin.php') { echo 'style="width:325px;padding-right:0px;"'; } else { echo 'style="width:1000px;padding-right:0px;"';} ?> >
