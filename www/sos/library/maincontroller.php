<?	
	include_once "syscontroller.php";
	
	class maincontroller extends syscontroller
	{		
		var $db;
		var $usertype;

		function __construct()
		{
			parent::__construct();
			$this->opendatabaseconnection();
		}
		
		function run() 
		{ 
			parent::run();		
			if ($this->checklogin && !$this->login()) $this->gotopage('login');
		
		}
		
		function __destruct()
		{
			if (isset($this->db) && $this->db != null)
			{
				$this->db->close();
				$this->db = null;
			}			
		}
		
		function setsysparam() 
		{ 
			parent::setsysparam();
			
			//db setting
			$this->sysparam['db']['server'] 		= 'NSUDBS';
			$this->sysparam['db']['name'] 			= 'webdev';
			$this->sysparam['db']['user'] 			= 'sos';
			$this->sysparam['db']['password'] 		= 'S0s#0k';	
			
			//database to send short message service
			$this->sysparam['dbsms']['server'] 		= '192.168.10.201';
			$this->sysparam['dbsms']['name'] 		= 'SOS2';
			$this->sysparam['dbsms']['user'] 		= 'sa';
			$this->sysparam['dbsms']['password'] 	= 'sa123';	
			
			//application parameter			
			$this->sysparam['app']['bcurl']				= "http://webdev.sophiemartin.com/sos/index.php";
			$this->sysparam['app']['mbrurl']			= "http://www.sophiemobile.com";
			$this->sysparam['app']['mbrdisclaimer'] 	= "Persyaratannya sebagai berikut bla...bla...bla..."; 
			
			$this->sysparam['appmsg']['bcaccountsuspend']	= "account member anda ditangguhkan, silahkan hubungi admin Sophie Online Shopping";
			
			//email parameter
			$this->sysparam['email']['bcneworder']['subject'] 		= "New Order from [mbrname]";
			$this->sysparam['email']['bcneworder']['body']			= 
				"Member #[mbrno] ([mbrname]) baru pesan online lewat BC anda. " . 

			    "Silahkan ke backoffice anda supaya pesanannya bisa divalidasikan:\n\n" .
				"[bcurl]";
				
			$this->sysparam['email']['bcnewpassword']['subject'] 	= "Password baru Sophie Online Shopping";
			$this->sysparam['email']['bcnewpassword']['body']		= 
				"Password baru anda adalah: [newpassword]\n" . 
			    "Silahkan untuk mencoba login [bcurl]";
																				
			$this->sysparam['session']['userid'] 	= 'userid';
			$this->sysparam['session']['usertype'] 	= 'usertype';
			$this->sysparam['session']['salesid'] 	= 'salesid';
			$this->sysparam['session']['smuserid'] 	= 'kdmember'; //from sophiemobile session
			
			//if you change the values below, you need to update the procedure or view on database
			//sync requestid
			$this->sysparam['sync']['order']		= 'order';
			$this->sysparam['sync']['cancel']		= 'cancel';
			
			$this->sysparam['payment']['visa']		= "1";
			
			//cancel code 
			$this->sysparam['cancelcode']['technicalerror']	= 0;
			$this->sysparam['cancelcode']['bymember']		= 1;
			$this->sysparam['cancelcode']['latepayment']	= 2;
			$this->sysparam['cancelcode']['emptystock']		= 3;
			$this->sysparam['cancelcode']['revisi']			= 4;
			
			//list of sales status
			$this->sysparam['salesstatus']['openorder']		= 1;
			$this->sysparam['salesstatus']['ordered']		= 2;
			$this->sysparam['salesstatus']['bypassed']		= 3;
			$this->sysparam['salesstatus']['inprogress']	= 4;
			$this->sysparam['salesstatus']['edited']		= 5; 
			$this->sysparam['salesstatus']['validated']		= 6; //bc sudah validate / mbr sudah confirm perubahan
			$this->sysparam['salesstatus']['confirmed']		= 7; //saat member pilih method pembayaran
			$this->sysparam['salesstatus']['paid']			= 8; //ini smi yang update		
			$this->sysparam['salesstatus']['ready']			= 9; //ready to pick
			$this->sysparam['salesstatus']['delivered']		= 10; //bc yang update kalo barang sudah di delivered. 
			$this->sysparam['salesstatus']['clear']			= 11; //bc yang update kalo barang sudah di delivered. 
			$this->sysparam['salesstatus']['cancelled']		= 0; //bila member membatalkan dari perubahan pesanan.
			
			
			//list of purch status
			$this->sysparam['purchstatus']['openorder']		= 1;
			$this->sysparam['purchstatus']['ordered']		= 2;
			$this->sysparam['purchstatus']['onorder']		= 3;
			$this->sysparam['purchstatus']['delivered']		= 9;
			$this->sysparam['purchstatus']['clear']			= 11;
			$this->sysparam['purchstatus']['cancelled']		= 0;
		}
				
		function opendatabaseconnection() 
		{
			if(!isset($this->db))
			{
				$this->db = new MsSQL($this->sysparam['db']['server'],$this->sysparam['db']['name'],$this->sysparam['db']['user'],$this->sysparam['db']['password']);
			}
		}		
		
		function userid() 
		{
			return (isset($_SESSION[$this->sysparam['session']['userid']]) ? $_SESSION[$this->sysparam['session']['userid']] : '');
		}
		
		function login()
		{			
	
			return isset($_SESSION[$this->sysparam['session']['userid']]) && $_SESSION[$this->sysparam['session']['usertype']] == $this->usertype;
		}	
		
		function gotopage($page,$param = '') { /* inherited */ }	
		
		function updatesalesstatus($salesid, $status, $cancelcode = 0)
		{
			$date = getdate();
			
			switch($status)
			{
				case $this->sysparam['salesstatus']['ordered']:
				case $this->sysparam['salesstatus']['ready']:
				case $this->sysparam['salesstatus']['delivered']:
				case $this->sysparam['salesstatus']['clear']:
				case $this->sysparam['salesstatus']['inprogress']:	
				case $this->sysparam['salesstatus']['cancelled']:
				case $this->sysparam['salesstatus']['edited']:
				case $this->sysparam['salesstatus']['validated']:
					$sql = 'exec sp_updateSalesStatus ' . $this->queryvalue($salesid) . ',' . $status . ',' . $cancelcode;
					$this->db->execute($sql);
					break;
			}
		}
		
		function sendemail($from, $to, $subject, $body)
		{
			$sql = "insert into " . $this->sysparam['table']['email'];
			$sql.= "([from],[to],subject,body,createdDate) values ";
			$sql.= "(" . $this->queryvalue($from);
			$sql.= "," . $this->queryvalue($to);
			$sql.= "," . $this->queryvalue($subject);
			$sql.= "," . $this->queryvalue($body);
			$sql.= ",getdate())";
			
			$this->db->execute($sql);
		}
		
		function sendsms($phone,$msg)
		{
			$sql = "insert into " . $this->sysparam['table']['sms'];
			$sql.= "(phone,message,createdDate) values ";
			$sql.= "(" . $this->queryvalue($phone);
			$sql.= "," . $this->queryvalue($msg);
			$sql.= ",getdate())";
			
			$this->db->execute($sql);
		}
	}
?>
