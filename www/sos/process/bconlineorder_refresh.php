<?
include_once "../library/bccontroller.php";
include_once "bconlineorder.php";
$obj = new bconlineorder;
$obj->loaddata();
echo $obj->neworders();
echo '--datasplit--';
echo $obj->orderstofollowup();
echo '--datasplit--';
echo $obj->orderstodeliver();
	
?>