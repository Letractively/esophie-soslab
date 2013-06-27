<?
	class mbrpaymentconfirm extends controller
	{	
		var $salesid;
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $mbrno;
		var $mbrname;
		var $mbraddress;
		var $totalorder;
		var $discount;
		var $paymentcharge;
		var $paymentname;
		var $paymentmode;
		var $paymentto;
		var $paymentdesc;
		var $merchantid;
		var $currencycode;
		var $returnurl;
		var $password;
		var $totalbayar;
		var $status;
		var $userstatus;
		var $orderdate;
		var $timeleft;
		var $virtualaccount;
                
                var $urlforward;
                var $urlsimulate;
		
		function run() 
		{
			parent::run();	
                        
                        $this->urlsimulate = $this->sysparam['paygate']['urlsimulate'];
                        
			$this->salesid = $this->param['salesid'];
			if ($this->salesid == '') $this->gotohomepage();
                        
                        $this->urlforward = $this->sysparam['paygate']['urlforward'] . urlencode($this->salesid);

			switch($this->action)
			{
				case "none":					
					$sql = "select status from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
					$status = $this->db->executescalar($sql);
					if ($status != $this->sysparam['salesstatus']['validated'])
						$this->gotohomepage();
					break;
			}
			$this->load();
                        
                        if ($this->action == "simulate") $this->SimulatePayment();
		}		
		
		function load()
		{
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{					
				
				$this->timeleft 		= $rs->value("timeleftpaid");
				$this->virtualaccount           = $rs->value("virtualaccount");
				
				$this->mbrno 			= $rs->value('kodemember'); 
				$this->mbrname 			= $rs->value('namamember'); 
				$this->mbraddress 		= $rs->value('alamat'); 
				
				$this->paymentcharge 	= $rs->value('paymentcharge'); 
				$this->paymentname 		= $rs->value('paymentname'); 
				$this->paymentmode 		= $rs->value('paymentmode'); 

				$sql = "Select * from paymentmode with (NOLOCK)  where paymentmode = " . $this->queryvalue($this->paymentmode);
				$rs1 = $this->db->query($sql);			
				if ($rs1->fetch()) 
				{
					$this->merchantid 	= $rs1->value('merchantid'); 
					$this->currencycode = $rs1->value('currencycode'); 					
					$this->returnurl 	= $rs1->value('returnurl'); 
					$this->password 	= $rs1->value('password'); 
					$this->paymentto 	= $rs1->value('paymentto'); 
					$this->paymentdesc 	= $rs1->value('description');
				}
				$rs1->close();
				
				$this->totalorder 		= $rs->value('totalorder'); 
				$this->discount 		= $rs->value('discount'); 
				$this->totalbayar 		= $rs->value('totalbayar'); 

				$this->bcno 			= $rs->value('kodebc'); 
				$this->bcname 			= $rs->value('namabc'); 
				$this->bcaddress 		= $rs->value('alamatbc'); 
				
				$this->orderdate 		= $this->valuedatetime($rs->value('orderdate')); 
				$this->userstatus		= $rs->value('userstatus'); 
				$this->status			= $rs->value('status'); 
						
			}
			else
			{
				$rs->close();
				$this->gotopage('memberinfo');
			}
			$rs->close();
		}
		
                function SimulatePayment ()
		{
                    $userid = "bot31025";
                    $password = "p@ssw0rd";
                    $signature = sha1(md5($userid . $password .  $this->salesid));
                    
                    $sql = "select trxref from paymenttable with (NOLOCK) where salesid = " . $this->queryvalue($this->salesid);
                    $rs = $this->db->query($sql);			
                    if ($rs->fetch()) 
                    {
                        $xml = "<faspay>";
                        $xml.= "<request>Payment Notification</request>";
                        $xml.= "<trx_id>" . $rs->value("trxref") . "</trx_id>";
                        $xml.= "<merchant_id>" . $this->merchantid . "</merchant_id>";
                        $xml.= "<merchant>SOPHIE PARIS</merchant>";
                        $xml.= "<bill_no>" . $this->salesid . "</bill_no>";
                        $xml.= "<payment_reff>dummyref123</payment_reff>";
                        $xml.= "<payment_date>" . date("Y-m-d H:i:s") . "</payment_date>";
                        $xml.= "<payment_status_code>2</payment_status_code>";
                        $xml.= "<payment_status_desc>Dummy success notification</payment_status_desc>";
                        $xml.= "<signature>" . $signature . "</signature>";
                        $xml.= "</faspay>";
                        
                        $url = $this->urlsimulate;
                        $ch = curl_init($url);

                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $response = curl_exec($ch);
                        curl_close($ch);
                    }
                    
                    return $response;
                }
                
		function SimulateATMPayment ($vanumber)
		{
			$bOk = false;
			$stan = '1234567';
			$trxdate = '20130125';
			$trxref = $vanumber;
			$salt = $this->JatisSaltIt($vanumber);
			
			$hashinit = $this->JatisHashItVAInquiry($vanumber, $stan, $trxdate, $salt);
			$urlinit = "http://paygate.sophieparis.com/jatis/vainquiry?hash=".$hashinit."&vanumber=".$vanumber."&stan=".$stan."&trxdate=".$trxdate;
			$result = file_get_contents($urlinit);
			if (strlen($result) > 0)
			{
				$bits = explode("|", $result, 6);
				if (sizeof($bits) > 5 && $bits[1] == "00")
				{
					$amount = $bits[4];
					$trxref = $bits[5];
					$bOk = true;
				}
			}
			
			if (!$bOk) return "Init failed! ".$result;

			$hashpay = $this->JatisHashItVAPayment($vanumber, $stan, $amount, $trxdate, $trxref, $salt);
			$urlpayment = "http://paygate.sophieparis.com/jatis/vapayment?hash=".$hashpay."&vanumber=".$vanumber."&stan=".$stan."&trxdate=".$trxdate."&trxref=".$trxref."&amount=".$amount;
			$result = file_get_contents($urlpayment);
			if (strlen($result) > 0)
			{
				$bits = explode("|", $result, 4);
				if (sizeof($bits) > 3 && $bits[1] == "00")
				{
					$bOk = true;
				}
			}
			
			if (!$bOk) return "Payment failed! ".$result;
			
			return "Payment OK : amount=" . $amount . " - " . $result; 
		}
		
						
		function JatisSaltIt($vanumber)
		{
		    // Membuat salt dari digit 5,6,15,16 dari va number
		    // Contoh : salt = 0124
		    $salt = substr($vanumber, 4,1).substr($vanumber, 5,1).substr($vanumber, 14,1).substr($vanumber, 15,1);
		    
		    // Salt di mod 7, sisipkan ke digit akhir salt
		    // Contoh : salt = 01245
		    $salt = $salt . ($salt % 7);
		    
		    return $salt;
		}
		
		function JatisHashItVAInquiry($vanumber, $stan, $trxdate, $salt)
		{
		    // parameter berikut di encrypt menggunakan md5 untuk inquiry
		    $inqHash = md5(strtoupper("vanumber=$vanumber&stan=$stan&trxdate=$trxdate&$salt"));
		    return $inqHash;
		}
		
		function JatisHashItVAPayment($vanumber, $stan, $amount, $trxdate, $trxref, $salt)
		{
		    // parameter berikut di encrypt menggunakan md5 untuk inquiry
		    $inqHash = md5(strtoupper("vanumber=$vanumber&stan=$stan&amount=$amount&trxdate=$trxdate&trxref=$trxref&$salt"));
		    return $inqHash;
		}
	}
?>
