<?
	include_once "database.php";
	include_once "maincontroller.php";
	
	class controller extends maincontroller
	{			
		function debug() { return false; }	
		function systemmaintenance() { return false; }	
		function setsysparam()
		{
			parent::setsysparam();														
		}
		
		function run()
		{
			$this->usertype = 2;
			parent::run();
		}
		
		function gotopage($page,$param = '')
		{
			switch(strtolower($page))
			{
				case 'login' 			: header("location:index.php" . ($param != '' ? '?' . $param : '')); break;	
				case 'onlineorder'		: header("location:bconlineorder.php" . ($param != '' ? '?' . $param : '')); break;	
				case 'editorder'		: header("location:bceditorder.php" . ($param != '' ? '?' . $param : '')); break;
				case 'ordertambahan'	: header("location:bcordertambahan.php" . ($param != '' ? '?' . $param : '')); break;	
				case 'myorder'			: header("location:bcmyorder.php" . ($param != '' ? '?' . $param : '')); break;
				case 'syncorder'		: header("location:bcsyncorder.php" . ($param != '' ? '?' . $param : '')); break;				
			}
		}
		
		function getRandomPassword($digit = 8)
		{
			$listchar = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$ret = '';
			while (strlen($ret) < $digit)
			{
				$newchar = substr($listchar,rand(0,strlen($listchar)-1),1);
				$pos = strpos($ret, $newchar);
				if ($pos === false)
				{
					$ret .= $newchar;	
				}
			}
			return $ret;
		}
	}
?>