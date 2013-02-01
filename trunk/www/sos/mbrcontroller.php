<?
	include_once "library/mbrcontroller.php";
	include_once "process/" . basename($_SERVER["PHP_SELF"]);
	$classname = str_replace(".php","",basename($_SERVER["PHP_SELF"]));

	$ctrl = new $classname;
	$ctrl->run();	
?>