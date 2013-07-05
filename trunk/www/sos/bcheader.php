<?include_once "bccontroller.php";?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ind" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<title>Sophie Online Orders - Backoffice BC</title>
	<meta name="viewport" content="width=360;"/> 
	<link rel="icon" type="image/ico" href="images/favicon.ico"/>
        <link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon-precomposed.png" />
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
	<link type="text/css" rel="stylesheet" href="css/global.css"/>
	<link type="text/css" rel="stylesheet" href="css/calendar.css"/>
	<script type="text/javascript" src="script/global.js"></script>
	<script type="text/javascript" src="script/calendar.js"></script>
        <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-42095429-2', 'sophiemobile.com');
            ga('send', 'pageview');

        </script>
	</head>
	<body>
	<form id="frmmain" name="frmmain" action="<?=$ctrl->filename()?>" method="post">
		<center>		
		<img src="images/logo.png" class="logo" alt="Sophie Online Shopping"/>
		<input type="hidden" id="pageaction" name="pageaction">		
		<div name="MyCalendar" id="MyCalendar"></div>	
		<?include_once "bcmenu.php";?>
		<div class="boxmain" <? if ($ctrl->filename() == 'bclogin.php') { echo 'style="width:325px;padding-right:0px;"'; } else { echo 'style="width:1000px;padding-right:0px;"';} ?> >
