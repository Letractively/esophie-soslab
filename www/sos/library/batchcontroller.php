<?	
	include_once "maincontroller.php";
	
	class batchcontroller extends maincontroller
	{		
		var $db;
		var $usertype;

		function run() 
		{ 
			$this->checklogin = false;
			parent::run();		
		}
		
		function orderBypassed() 
		{ 
			$sql = "exec sp_salesByPassed " ;
			$sql.= "," . $this->sysparam['app']['bcincludeppn'];
			$sql.= "," . $this->sysparam['app']['bcvalidate'];
			$this->db->execute($sql);
		}		
		
		function syncChecking()
		{
			$sql = "exec sp_checkSyncOrder " ;
			$this->db->execute($sql);
			
			//bisa untuk order dan cancel order
			/*
				untuk yang order
				cek bila 
				- sukses jadi validated 
				- edited mesti di confirm user
				- failed kalo bypassed diulang sampai x kali
				- failed bukan bypassed ?
				untuk yang cancel maka cancel order.
			*/
		}
		
		function orderPayment()
		{
			//cek apakah order lebih dari maximum payment date
		}
		
		function sendingemail()
		{
			//send email pake PHP
		}
		
		function sendingsms()
		{
			//ke database sms SMI
		}
		
	}
	
?>