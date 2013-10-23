<?
	class bcchangepassword extends controller
	{	
		var $errmsg;
		var $pageindex;
		function run() 
		{	
			$this->checklogin = false;			
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/newpassword";
                        $this->gatitle = "Order - BC - Change password page";
                        // GOOGLE ANALYTICS PAGE TRACKING
			
			$this->errmsg = '';
			$this->pageindex = 1;
                        
                        if ($this->action == "ok")
			{
                            if (!isset($this->param["userid"])) 
                                $this->errmsg = 'Kode BC harus diinput';
                            else if (!isset($this->param["email"])) 
                                $this->errmsg = 'Email harus diinput';
                            else if (!isset($this->param["oldpwd"])) 
                                $this->errmsg = 'Password lama harus diinput';
                            else if (!isset($this->param["newpwd1"]) || strlen($this->param["newpwd1"]) < 6) 
                                $this->errmsg = 'Password baru harus lebih dari 6 char';
                            else if (!isset($this->param["newpwd2"]) || $this->param["newpwd2"] != $this->param["newpwd1"]) 
                                $this->errmsg = 'Password baru kedua bedah dari password baru pertama';
                            else {

                                $sql = "select email,suspend from BCTable ";
				$sql.= " where kodebc = " . $this->queryvalue($this->param["userid"]);
				$sql.= " and email = " . $this->queryvalue($this->param["email"]);
                                $sql.= " and password = " . $this->queryvalue(md5($this->param["oldpwd"]));
				
				$rs = $this->db->query($sql);
				if($rs->fetch())
				{
					$bcemail = $rs->value('email');
					if ($rs->value('suspend') == 1)
					{
						$this->errmsg = $this->sysparam['appmsg']['bcaccountsuspend'];
					}
					else
					{
						$newpass = $this->param["newpwd1"];
						
						$sql = "update BCTable set password = " . $this->queryvalue(md5($newpass));
						$sql.= " where kodebc = " . $this->queryvalue($this->param["userid"]); 
						$this->db->execute($sql);

						$emailfrom = $this->db->execute('select top 1 emailfrom from sysparamtable with (nolock)');
						
						$this->pageindex = 2;
						$body = $this->sysparam['email']['bcnewpassword']['body'];
						$body = str_replace("[newpassword]",$newpass,$body);
						$body = str_replace("[bcurl]",$this->sysparam['app']['bcurl'],$body);	
						$this->sendemail($emailfrom,$bcemail,$this->sysparam['email']['bcnewpassword']['subject'],$body);									
					}
				}
				else
				{
					$this->errmsg = 'Kode BC / email / password yang anda masukkan salah';
				}
				$rs->close();
                            }
			}
		}		
	}
?>