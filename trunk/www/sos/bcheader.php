<? header('Content-Type: text/html; charset=utf-8');
include_once "bccontroller.php";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ind" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Sophie Online Orders - Backoffice BC</title>
	<meta name="viewport" content="width=360"/> 
	<link rel="icon" type="image/ico" href="images/favicon.ico"/>
        <link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon-precomposed.png" />
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
	<link type="text/css" rel="stylesheet" href="css/global.css"/>
	<link type="text/css" rel="stylesheet" href="css/calendar.css"/>
        <? include_once "bcanalytics.php"; ?> 
        <script type="text/javascript" src="script/global.js?v3"></script>
	<script type="text/javascript" src="script/calendar.js"></script>
        <script type="text/javascript">
            function checkchrome() {
                if (!window.chrome) {
                    document.getElementById('chromewarning').style.display = 'block';
                }
            }
        </script>
	</head>
	<body onload="checkchrome();">
        <div id="chromewarning" class="chromewarning" style="">This website works best with a Chrome browser! Please download it there : <a href="http://www.google.com/chrome/">www.google.com/chrome</a></div>
	<form id="frmmain" name="frmmain" action="<?=$ctrl->filename()?>" method="post" accept-charset="utf-8">		
                <input type="hidden" id="pageaction" name="pageaction"/>		
		<div name="MyCalendar" id="MyCalendar"></div>
                <div class="header">
                    <img src="images/logo.png" class="logo" alt="Sophie Online Shopping"/>	
                    <?include_once "bcmenu.php";?></div>
		<div class="boxmain" <? if ($ctrl->filename() == 'bclogin.php' || $ctrl->filename() == 'bcnewpassword.php') {
                            echo 'style="width:325px;padding-right:0px;"'; 
                        } else { 
                            echo 'style="width:1000px;padding-right:0px;"';   
                        } ?> >
