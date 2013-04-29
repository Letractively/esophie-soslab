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
				case 'login' 			: header('location:index.php' . ($param != '' ? '?' . $param : '')); break;	
				case 'onlineorder'		: header('location:bconlineorder.php' . ($param != '' ? '?' . $param : '')); break;	
				case 'ordertambahan'	: header('location:bcordertambahan.php' . ($param != '' ? '?' . $param : '')); break;	
				case 'myorder'			: header('location:bcmyorder.php' . ($param != '' ? '?' . $param : '')); break;
				case 'vieworder'		: header('location:bcvieworder.php' . ($param != '' ? '?' . $param : '')); break;
				case 'report2'			: header('location:bcreport02.php' . ($param != '' ? '?' . $param : '')); break;
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
		
		function colorstatus($status)
		{
			$ret = 'blank'; //lihat global.css style ".colorxx"
			
			switch ($status)
			{
				case $this->sysparam['salesstatus']['ordered']:
					$ret = '01';
					break;
				case $this->sysparam['salesstatus']['cancelled'] : 
					$ret = '00';  break;				
				case $this->sysparam['salesstatus']['bypassed'] : 
				case $this->sysparam['salesstatus']['inprogress'] : 
					$ret = '02';  break;
				case $this->sysparam['salesstatus']['edited'] :  
					$ret = '05';  break;
				case $this->sysparam['salesstatus']['validated'] : 
				case $this->sysparam['salesstatus']['confirmed'] : 
					$ret = '06';  break;
				case $this->sysparam['salesstatus']['paid'] : 
					$ret = '08';  break;
				case $this->sysparam['salesstatus']['ready'] : 
					$ret = '09';  break;
				case $this->sysparam['salesstatus']['delivered'] : 
					$ret = '10';  break;
			}
			return $ret;
		}
		
		function colorstatuslabel($status)
		{
			$ret = '';//'01';
						
			switch ($status)
			{
				case $this->sysparam['salesstatus']['ordered']:
					$ret = 'Order Baru';
					break;
				case $this->sysparam['salesstatus']['cancelled'] : 
					$ret = 'Cancelled'; break; //'00';
				case $this->sysparam['salesstatus']['bypassed'] : 
				case $this->sysparam['salesstatus']['inprogress'] : 
					$ret =  'Dalam Proses'; break; //'02';
				case $this->sysparam['salesstatus']['edited'] :  
					$ret =  'Edited'; break; //'05';
				case $this->sysparam['salesstatus']['validated'] : 
					$ret = 'Waiting payment'; break; //'06';
				case $this->sysparam['salesstatus']['confirmed'] : 
					$ret = 'Payment confirmed'; break; //'06';
				case $this->sysparam['salesstatus']['paid'] : 
					$ret = 'Paid'; break; //'08'; 
				case $this->sysparam['salesstatus']['ready'] : 
					$ret = 'Ready for Pickup'; break; //'09';
				case $this->sysparam['salesstatus']['delivered'] : 
					$ret = 'Delivered'; break; //'10';
				case $this->sysparam['salesstatus']['clear']:
					$ret = 'Clear';
					break;
			}
			return strtoupper($ret);
		}
		
		function cancelreason($cancelcode)
		{
			$ret = '';
			switch($cancelcode)
			{
				case $this->sysparam['cancelcode']['bymember']: 
					$ret = 'Member telah membatalkan pesanan tersebut.';
					break;
				case $this->sysparam['cancelcode']['latepayment']:
					$ret = 'Member tidak membayar dalam waktu yang ditentukan.';
					break;
				case $this->sysparam['cancelcode']['emptystock'] :
					$ret = 'Stock Sophie lagi kosong.';
					break;
				case $this->sysparam['cancelcode']['revisi']:
					$ret = 'Member tidak confirm edited order.';
					break;
				case $this->sysparam['cancelcode']['technicalerror']:
					$ret = 'ada masalah teknis.';
					break;
			}
			return $ret;
		}
	}
?>