<?
	include_once "database.php";
	include_once "maincontroller.php";
	
	class controller extends maincontroller
	{			
		var $salesid;
		var $errmsg;
		var $mbrmsg;
		var $disclaimercheck; //
		
		function __construct()
		{
			parent::__construct();
			$this->disclaimercheck = true;
		}
		
		function debug() { return false; }	
		function systemmaintenance() { return false; }	
		function setsysparam()
		{
			parent::setsysparam();
				
			$this->sysparam['memberstatus']['firstlogin']		= "firstlogin";
			$this->sysparam['memberstatus']['suspend']		= "suspend";
			$this->sysparam['memberstatus']['neworder']		= "neworder";			
		}
		
		function run()
		{
			$this->usertype = 1;

			parent::run();
                        
                        $this->checkLogin();

			if ($this->disclaimercheck)
			{
				switch($this->disclaimerchecking())
				{	
					case 'disclaimer': $this->gotopage('disclaimer'); break;
					case 'suspend' : $this->gotopage('suspend');break;
					case 'continue' : break; 
					default: break;
				}
			}
       
		}
                
                function checkLogin() {
                    if ($this->checklogin && !$this->login())
                    {
                        $this->singlesignonsophiemobile ();
                    }
                    
                    if ($this->checklogin && !$this->login())
                    {
                        $this->gotopage('login');
                    }
                    
                    if ($this->checklogin)
                    {
                        $this->checkmembermapping();
                    }
                }
                
                function singlesignonsophiemobile()
		{
                    try
                    {
			if (isset($this->param['sssm']))
                        {
                            $sssm = base64_decode($this->param['sssm']);
                            if (strlen($sssm) > 0)
                            {
                                $sssm_array = explode(":", $sssm);
                                if (sizeof($sssm_array) == 2 && strlen($sssm_array[0]) > 0)
                                {
                                    $kdmember = $sssm_array[0];
                                    
                                    $sql1 = "SELECT count(*) FROM memberTable";
                                    $sql1.= " WHERE kodemember = ". $this->queryvalue($kdmember);
                                    if ($this->db->executeScalar($sql1) == 0)
                                    {
                                        // Update/import member data and mapping
                                        $this->importmember($kdmember);
                                    }
                            
                                    $sql2 = "SELECT norekening FROM memberTable";
                                    $sql2.= " WHERE kodemember = ". $this->queryvalue($kdmember);
                                    $rs = $this->db->query($sql2);
                                    if ($rs->fetch())
                                    {
                                        $nbrekening = $rs->value("norekening");
                                    }
                                    $rs->close();
                                      
                                    if (strlen($nbrekening) > 4)
                                    {
                                        // sssm = md5([memberid]:[pwd(5)][pwd(2)][pwd(3)])
                                        $salt = substr($nbrekening,4,1) . substr($nbrekening,1,1) . substr($nbrekening,2,1);
                                        $mysssm = md5($kdmember.":".$salt);

                                        if (strcmp($sssm,$mysssm) == 0)
                                        {
                                            $_SESSION[$this->sysparam['session']['userid']] = $kdmember;
                                            $_SESSION[$this->sysparam['session']['usertype']] = $this->usertype;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        // If single sign/on failed => silent fail (redirect to login)
                        if ($this->debug()) echo $e; 
                    }
		}
                
                function importmember($kdmember)
                {
                    try
                    {
                        // Create or update the BC mapping for this new member
                        $sql = "exec sp_sos_IMPORTMEMBER " . $this->queryvalue($kdmember);
                        //$sql = "INSERT INTO memberTable SELECT ". $this->queryvalue($kdmember) .
                        //        ", NULL, 0, 'MBR BLABLA', 'Jalan Bla Bla Jakarta', '082122821440', 'victor@sophieparis.com', '123456789'" .
                        //        " WHERE NOT EXISTS (SELECT 'x' FROM memberTable WHERE kodemember = ". $this->queryvalue($kdmember) .")";
                        if ($this->debug()) echo $sql;
                        $this->db->execute($sql);
                    }
                    catch (Exception $e)
                    {
                        // If no update/import fail silently
                        if ($this->debug()) echo $e; 
                    }
                }
                
                function checkmembermapping()
                {
                    try
                    {
                        $sql = "SELECT count(bc.kodebc) FROM BCTable bc";
                        $sql.= " INNER JOIN mappingTable mt ON bc.kodebc = mt.kodebc";
                        $sql.= " WHERE mt.kodemember = ". $this->queryvalue($this->userid());
                        //echo $sql; exit;
                        $nb = $this->db->executeScalar($sql);
                        if ($nb == 0)
                        {
                            $this->gotopage('notavailable');
                        }
                    }
                    catch (Exception $e)
                    {
                        // If no error, fail silently
                        if ($this->debug()) echo $e; 
                    }
                }
		
		function disclaimerchecking()
		{
			$ret = '';
			$sql = "select suspend,acceptdate from memberTable";
                        $sql.= " where kodemember = " . $this->queryvalue($this->userid());				
			$rs = $this->db->query($sql);
			if ($rs->fetch())
			{	
				if ($rs->value("suspend") == 0)
				{
					if (is_null($rs->value("acceptdate"))) $ret = 'disclaimer';
					else $ret = 'continue';					
				}
				else $ret = 'suspend';
			}
				
			$rs->close();
			return $ret;
		}
		
		function setmbrmsg()
		{	
			$sql = "select name from membertable where KodeMember = " . $this->queryvalue($this->userid());
			$mbrname = $this->db->executescalar($sql);	
			
			$this->mbrmsg['title'] = '';
			$this->mbrmsg['body']  = '';
			$this->mbrmsg['color'] = '';
										
			if ($this->salesid != '')
			{
				$sql = 'select * from vw_salestable where salesid = ' . $this->queryvalue($this->salesid);
				$rs = $this->db->query($sql);			
				$rs->fetch();
				
				$this->mbrmsg['color'] = $this->colorstatus($rs->value('status'));
				switch ($rs->value('status'))
				{
					case $this->sysparam['salesstatus']['cancelled'] : 
						$this->mbrmsg['title'] = 'Mohon maaf, Order anda telah dibatalkan';  
						$this->mbrmsg['body'] = 'Online order #' . $this->salesid . ' telah dibatalkan karena ';
						switch($rs->value('cancelcode'))
						{
							case $this->sysparam['cancelcode']['bymember']: 
								$this->mbrmsg['body'].= 'anda telah membatalkan pesanan tersebut.';
								break;
							case $this->sysparam['cancelcode']['latepayment']:
								$this->mbrmsg['body'].= 'pembayaran anda belum diterima di dalam waktu yang ditentukan';
								break;
							case $this->sysparam['cancelcode']['emptystock'] :
								$this->mbrmsg['body'].= 'stock Sophie lagi kosong.';
								break;
							case $this->sysparam['cancelcode']['revisi']:
								$this->mbrmsg['body'].= 'anda tidak setuju dengan revisi order dari kami';
								break;
							default:
								$this->mbrmsg['body'].= 'ada masalah teknis.';
						}
						$this->mbrmsg['link1label'] = 'PESAN LAGI';
						$this->mbrmsg['link1'] = 'mbrpilihitem.php';						
						break;
						
					case $this->sysparam['salesstatus']['openorder']:
						$this->mbrmsg['title'] = 'Selamat datang kembali ' . $mbrname . '!';  
						$this->mbrmsg['body'] = 'Anda sudah pernah melakukan order online sebelumnya, tetapi proses order belum selesai!';
						$this->mbrmsg['body'].= ' Apakah anda mau melanjutkan atau melakukan order baru?';
						$this->mbrmsg['link1label'] = 'ORDER BARU';
						$this->mbrmsg['link1'] = 'mbrneworder.php';
						$this->mbrmsg['link2label'] = 'LANJUT';
						$this->mbrmsg['link2'] = 'mbrvieworder.php?edit=1&salesid=' . $this->salesid;
						break;
						
					case $this->sysparam['salesstatus']['ordered'] : 
					case $this->sysparam['salesstatus']['bypassed'] : 
					case $this->sysparam['salesstatus']['inprogress'] :
						$this->mbrmsg['title'] = 'Order #' . $this->salesid . ' dikirim ke BC untuk di validasi...';  
						$this->mbrmsg['body'] = 'Validasi order anda sedang diproses oleh BC #' . $rs->value('kodebc') . '.';
						$this->mbrmsg['body'].= ' Anda akan menerima confirmation order ';
						
						$todaydate = date_parse(date('Y-m-d'));
						$maxvalidate = date_parse($rs->value('maxvalidatedate'));
						
						if ($maxvalidate['year'] == $todaydate['year'] &&
						    $maxvalidate['month'] == $todaydate['month'] &&
							$maxvalidate['day'] == $todaydate['day'])
						{
							$this->mbrmsg['body'].= 'dalam waktu 30-60 menit...';
						} else {
							$this->mbrmsg['body'].= ' besok sebelum jam ' . $maxvalidate['hour'] .':'. $maxvalidate['minute'];
						}
						
						break;
						
					case $this->sysparam['salesstatus']['edited'] : 
						$this->mbrmsg['title'] = 'Order #' . $this->salesid . ' telah dirubah!!'; 
						$this->mbrmsg['body'] = 'Mohon maaf, order anda telah kami revisi dikarenakan terdapat produk yang tidak tersedia.';
						$this->mbrmsg['body'].= ' Silahkan "Check Order" anda kembali dan pilih "OK" jika anda setuju atau pilih "Tolak" jika anda tidak setuju';
						$this->mbrmsg['link1label'] = 'CHECK ORDER';
						$this->mbrmsg['link1'] = 'mbrvieworder.php?salesid=' . $this->salesid;;
						break;
						
					case $this->sysparam['salesstatus']['validated'] : 
						$this->mbrmsg['title'] = 'Order #' . $this->salesid . ' anda telah divalidasi oleh BC!';  
						$this->mbrmsg['body'] = 'Silahkan lanjut ke pembayaran. Jika pembayaran belum diterima pukul ' . $this->valuedatetime($rs->value('maxpaiddate')) . 'WIB maka order andal otomatis batal.';
						$this->mbrmsg['link1label'] = 'BAYAR';
						$this->mbrmsg['link1'] = 'mbrpaymentconfirm.php?salesid=' . $this->salesid;
						break;
					
					case $this->sysparam['salesstatus']['confirmed'] :
						$this->mbrmsg['title'] = 'Terima kasih atas pembayaran anda!';  
						$this->mbrmsg['body'] = 'Pembayaran untuk order #' . $this->salesid . ' telah kami terima dan sedang diperiksa oleh team Kasir Sophie Paris.';
						$this->mbrmsg['body'].= ' Silahkan belanja lagi di Sophie Online Order!';
						$this->mbrmsg['link1label'] = 'PESAN LAGI';
						$this->mbrmsg['link1'] = 'mbrpilihitem.php';
						break;
						
					case $this->sysparam['salesstatus']['paid'] : 
						$this->mbrmsg['title'] = 'Terima kasih atas pembayaran anda!';  
						$this->mbrmsg['body'] = 'Pembayaran untuk order #' . $this->salesid . ' telah kami terima.';
						$this->mbrmsg['body'].= ' Order anda akan disiapkan oleh BC #'. $rs->value('kodebc') .' dalam waktu 2-3 hari.';
						$this->mbrmsg['body'].= ' Silahkan belanja lagi di Sophie Online Order!';
						$this->mbrmsg['link1label'] = 'PESAN LAGI';
						$this->mbrmsg['link1'] = 'mbrpilihitem.php';
						break;
                                        
                                        case $this->sysparam['salesstatus']['ready'] :
                                                $this->mbrmsg['title'] = 'Order #' . $this->salesid . ' sudah siap diambil di BC!';  
						$this->mbrmsg['body'] = 'Anda bisa menghubungi BC anda untuk menerima order Anda. Silhakan belanja lagi di Sophie Online Order!';
						$this->mbrmsg['link1label'] = 'PESAN ONLINE';
						$this->mbrmsg['link1'] = 'mbrpilihitem.php';
						break;
                                            
					case $this->sysparam['salesstatus']['delivered'] : 
					default:
						$this->mbrmsg['title'] = 'Selamat datang kembali ' . $mbrname . '!';  
						$this->mbrmsg['body'] = 'Terima kasih atas kepercayaan Anda. Silahkan klik link dibawah ini untuk belanja lagi di Sophie Online Order!';
						$this->mbrmsg['link1label'] = 'PESAN ONLINE';
						$this->mbrmsg['link1'] = 'mbrpilihitem.php';
						break;
				}
				$rs->close();
			}
			else
			{
				$this->mbrmsg['color'] = $this->colorstatus(1);
				$this->mbrmsg['title'] = 'Selamat datang kembali ' . $mbrname . '!';
				$this->mbrmsg['body'] = 'Silahkan klik link dibawah ini untuk belanja lagi di Sophie Online Order!';
				$this->mbrmsg['link1label'] = 'PESAN ONLINE';
				$this->mbrmsg['link1'] = 'mbrpilihitem.php';
			}
		}
		
		function checksalesid()
		{
			$this->salesid = isset($this->param['salesid']) ? $this->param['salesid'] : '';
			if ($this->salesid == '') $this->gotohomepage();
		}
		
		function checksalesopenorder()
		{
			$sql = 'select count(*) from salestable ';
			$sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
			$sql.= ' and status = ' . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
			$sql.= ' and salesid = ' . $this->queryvalue($this->salesid);
			if(!$this->db->executeScalar($sql)) $this->gotohomepage();
		}
		
		function gotohomepage()
		{
			$this->gotopage('orderhistory');	
			//header("location:mbrviewhistory.php");
			//exit;
		}
		
		function gotopage($page,$param = '')
		{
			switch(strtolower($page))
			{
				case 'login' 			: header("location:mbrlogin.php". ($param != '' ? '?' . $param : '')); break;
				case 'disclaimer' 		: header("location:mbrdisclaimer.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'suspend' 			: header("location:mbrsuspend.php" . ($param != '' ? '?' . $param : ''));  break;
                                case 'notavailable' 		: header("location:mbrnoaccess.php?action=nomapping" . ($param != '' ? '&' . $param : ''));  break;
				case 'orderhistory'             : header("location:mbrviewhistory.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'memberinfo' 		: header("location:mbrcekdata.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'inputitem' 		: header("location:mbrpilihitem.php" . ($param != '' ? '?' . $param : ''));  break;				
				case 'checkitem' 		: header("location:mbrvieworder.php?edit=1" . ($param != '' ? '&' . $param : ''));  break;
				case 'confirm' 			: header("location:mbrvieworder.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'paymentmethod'            : header("location:mbrpaymentmethod.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'paymentconfirm'           : header("location:mbrpaymentconfirm.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'paymentreceived'          : header("location:mbrpaymentreceived.php" . ($param != '' ? '?' . $param : ''));  break;
				case 'neworder'	 		: header("location:mbrneworder.php" . ($param != '' ? '?' . $param : ''));  break;
			}
			exit;
		}
	
		function printerrors()
		{
                        $ret = '';
			if (is_array($this->errmsg))
			{
				$ret = '<div class="errormessage" style="text-align:left">';
				$total = count($this->errmsg);
				if ($total)
				{
					$ret.="<ul>";
					foreach($this->errmsg as $colname => $errmsg)
					{
						$ret.= '<li>'. $errmsg . '</li>';
					}	
					$ret.="</ul>";
				} 
				$ret.="</div>";
			}
			else
			{
                                if ( $this->errmsg != '' )
				{
					$ret = '<div class="errormessage">' . ucfirst($this->errmsg) . '</div>';
				}
			}
			return $ret;
		}
		
		function colorstatus($status)
		{
			$ret = '01'; //lihat global.css style ".colorxx"
			
			switch ($status)
			{
				case $this->sysparam['salesstatus']['cancelled'] : 
					$ret = '00';  break;
				case $this->sysparam['salesstatus']['ordered'] : 
				case $this->sysparam['salesstatus']['bypassed'] : 
				case $this->sysparam['salesstatus']['inprogress'] : 
					$ret = '02';  break;
				case $this->sysparam['salesstatus']['edited'] :  
					$ret = '05';  break;
				case $this->sysparam['salesstatus']['validated'] : 
					$ret = '06';  break;
				case $this->sysparam['salesstatus']['confirmed'] : 
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
			$ret = 'Order Baru';//'01';
						
			switch ($status)
			{
				case $this->sysparam['salesstatus']['cancelled'] : 
					$ret = 'Batal'; break; //'00';
				case $this->sysparam['salesstatus']['ordered'] : 
				case $this->sysparam['salesstatus']['bypassed'] : 
				case $this->sysparam['salesstatus']['inprogress'] : 
					$ret =  'Dikirim ke BC'; break; //'02';
				case $this->sysparam['salesstatus']['edited'] :  
					$ret =  'Revisi'; break; //'05';
				case $this->sysparam['salesstatus']['validated'] : 
					$ret = 'Validasi BC'; break; //'06';
				case $this->sysparam['salesstatus']['confirmed'] : 
				case $this->sysparam['salesstatus']['paid'] : 
					$ret = 'Telah Bayar'; break; //'08'; 
				case $this->sysparam['salesstatus']['ready'] : 
					$ret = 'Siap diambil'; break; //'09';
				case $this->sysparam['salesstatus']['delivered'] : 
					$ret = 'Delivered'; break; //'10';
			}
			return $ret;
		}
	}
?>