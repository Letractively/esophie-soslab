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
			$this->sysparam['db']['server'] 		= '10.0.0.102';
			$this->sysparam['db']['name'] 			= 'webdev';
			$this->sysparam['db']['user'] 			= 'sos';
			$this->sysparam['db']['password'] 		= 'S0s#0k';	
			
			//short message service
                        $this->sysparam['dbsms']['url']                 = 'http://broadcast.jatismobile.com/smspush/send.aspx?userid=smartin&password=smartin123';

                        //payment gateway settings
                        $this->sysparam['paygate']['urlinit'] 		= "http://paygate.sophieparis.com/faspay/postdatatrx?salesid=";
                        $this->sysparam['paygate']['urlforward']        = "http://paygate.sophieparis.com/sophie/forwardredirect?salesid=";
                        $this->sysparam['paygate']['urlsimulate']       = "http://paygate.sophieparis.com/faspay/paymentnotification";
			
			//application parameter			
			$this->sysparam['app']['bcurl']                 = "http://order.sophiemobile.com/bclogin.php";
			$this->sysparam['app']['mbrurl']		= "http://order.sophiemobile.com";
			
			$this->sysparam['appmsg']['bcaccountsuspend']	= "Account member anda ditangguhkan, silahkan hubungi Sophie Care.";
			
			//email parameter
                        $this->sysparam['email']['host']                 = "10.0.0.17"; 
                        $this->sysparam['email']['port']                 = 25; 
                        $this->sysparam['email']['fromemail']            = "onlineorders@sophieparis.com"; 
                        $this->sysparam['email']['fromname']             = "Sophie Online Orders (NO REPLY)";             
				
			$this->sysparam['email']['bcnewpassword']['subject'] 	= "Password baru Sophie Online Shopping";
			$this->sysparam['email']['bcnewpassword']['body']	= "Password baru anda adalah: [newpassword]\n" . 
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
			$this->sysparam['cancelcode']['technicalerror']		= 0;
			$this->sysparam['cancelcode']['bymember']		= 1;
			$this->sysparam['cancelcode']['latepayment']		= 2;
			$this->sysparam['cancelcode']['emptystock']		= 3;
			$this->sysparam['cancelcode']['revisi']			= 4;
			
			//list of sales status
			$this->sysparam['salesstatus']['openorder']		= 1;
			$this->sysparam['salesstatus']['ordered']		= 2;
			$this->sysparam['salesstatus']['bypassed']		= 3;
			$this->sysparam['salesstatus']['inprogress']		= 4;
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
			return isset($_SESSION[$this->sysparam['session']['userid']]) && 
				isset($_SESSION[$this->sysparam['session']['usertype']]) &&
				$_SESSION[$this->sysparam['session']['usertype']] == $this->usertype;
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
                                case $this->sysparam['salesstatus']['confirmed']:
                                case $this->sysparam['salesstatus']['paid']:
				case $this->sysparam['salesstatus']['edited']:
				case $this->sysparam['salesstatus']['validated']:
					$sql = 'exec sp_updateSalesStatus ' . $this->queryvalue($salesid) . ',' . $status . ',' . $cancelcode;
					$this->db->execute($sql);
					break;
			}
		}
		
		function sendemail($from, $to, $subject, $body)
		{
			$sql = 'insert into emailtable ';
			$sql.= '([from],[to],subject,body,createdDate) values ';
			$sql.= '(' . $this->queryvalue($from);
			$sql.= ',' . $this->queryvalue($to);
			$sql.= ',' . $this->queryvalue($subject);
			$sql.= ',' . $this->queryvalue($body);
			$sql.= ',getdate())';
			
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
                
                function initfaspay($salesid)
                {
                    // Send HTTP request to paygate to initialize the payment
                    $urlpaygate = $this->sysparam['paygate']['urlinit'] . urlencode($salesid); 
                    $result = file_get_contents($urlpaygate);
                    
                    $data = json_decode($result);
                    // If response = OK
                    if (isset($data->response) && strcasecmp("OK",$data->response) == 0)
                    {
                        if (isset($data->trxref) && strlen($data->trxref) > 0)
                        {
                            $sql0 = "update salestable set virtualaccount = " . $this->queryvalue($data->trxref);
                            $sql0.= "where paymentmode = 'ATM' and salesid = " . $this->queryvalue($salesid);
                            $this->db->execute($sql);
                        }
                        
                        // Templates for validated orders
                        $emailTemplate = 'VLDORD2MBR';
                        $SMSTemplate = 'VLDORD2MBR';

                        // Send email and SMS
                        $sql = "exec sp_sendEmailAndSMS ";
                        $sql.= $this->queryvalue($salesid);
                        $sql.= ", 1, " . $this->queryvalue($emailTemplate); 
                        $sql.= ", '', " . $this->queryvalue($SMSTemplate);
                        $this->db->execute($sql);
                        
                         return true;
                    }
                    else return false;
                }
	}
?>
