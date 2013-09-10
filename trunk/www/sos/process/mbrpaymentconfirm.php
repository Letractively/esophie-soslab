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
                var $paymstatus;
                var $trxref;
                var $timeleftinit;
		
		function run() 
		{
			parent::run();	
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/member/order/payment/checkout";
                        $this->gatitle = "Member - Order checkout page";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			$this->salesid = $this->param['salesid'];
			if ($this->salesid == '') $this->gotohomepage();
                        
                        if ($this->action == 'forward')
                        {
                            $urlpaygate = $this->sysparam['paygate']['urlforward'] . urlencode($this->salesid);
                            
                            // Send HTTP request to paygate to initialize the payment
                            $result = file_get_contents($urlpaygate);
                            $data = json_decode($result);
                            
                            // If response = OK
                            if (isset($data->response) && strcasecmp("OK",$data->response) == 0)
                            {
                                if (isset($data->url) && strlen($data->url) > 0)
                                {
                                    header("location:" . $data->url);
                                    exit;
                                }
                            }
                        }
                        
                        else if ($this->action == 'back')
                        {
                           $this->gotopage('checkitem', 'salesid=' . $this->salesid);
                        }
                        
                        $sql = "select status, paymstatus, timeleftinit, trxref from vw_paymtable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{					
				$this->status			= $rs->value('status'); 
                                $this->timeleftinit 		= $rs->value("timeleftinit");
                                $this->paymstatus               = $rs->value('paymstatus');
                                $this->trxref                   = $rs->value('trxref');
                            
                                if ($this->status != $this->sysparam['salesstatus']['validated'])
                                    $this->gotohomepage();
                                
                                // init payment first if not yet initialized
                                if (($this->paymstatus == 0 || $this->paymstatus == 3) && $this->timeleftinit > 0)
                                    $this->initfaspay ($this->salesid);
                        }
                        
			$this->load();
                        
                        //if ($this->action == "simulate") $this->SimulatePayment();
		}		
		
		function load()
		{
			$sql = "select * from vw_paymtable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{					
				$this->orderdate 		= $this->valuedatetime($rs->value('orderdate')); 
				$this->userstatus		= $rs->value('userstatus'); 
				$this->status			= $rs->value('status'); 
                            
				$this->timeleft 		= $rs->value("timeleftpaid");
                                $this->timeleftinit 		= $rs->value("timeleftinit");
				$this->virtualaccount           = $rs->value("virtualaccount");
                                $this->paymstatus               = $rs->value('paymstatus');
                                $this->trxref                   = $rs->value('trxref');
                               
				$this->mbrno 			= $rs->value('kodemember'); 
				$this->mbrname 			= $rs->value('namamember'); 
				$this->mbraddress 		= $rs->value('alamat'); 
				
				$this->paymentcharge            = $rs->value('paymentcharge'); 
				$this->paymentname 		= $rs->value('paymentname'); 
				$this->paymentmode 		= $rs->value('paymentmode'); 

                                $this->merchantid               = $rs->value('merchantid'); 
                                $this->currencycode             = $rs->value('currencycode'); 					
                                $this->returnurl                = $rs->value('returnurl'); 
                                $this->password                 = $rs->value('password'); 
                                $this->paymentto                = $rs->value('paymentto'); 
                                $this->paymentdesc              = $rs->value('description');
				
				$this->totalorder 		= $rs->value('totalorder'); 
				$this->discount 		= $rs->value('discount'); 
				$this->totalbayar 		= $rs->value('totalbayar'); 

				$this->bcno 			= $rs->value('kodebc'); 
				$this->bcname 			= $rs->value('namabc'); 
				$this->bcaddress 		= $rs->value('alamatbc'); 
						
			}
			else
			{
				$rs->close();
				$this->gotohomepage();
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
 
	}
?>
