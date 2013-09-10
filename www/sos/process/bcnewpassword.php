<?
	class bcnewpassword extends controller
	{	
		var $errmsg;
		var $pageindex;
		function run() 
		{	
			$this->checklogin = false;			
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/newpassword";
                        $this->gatitle = "BC - Change password page";
                        // GOOGLE ANALYTICS PAGE TRACKING
			
			$this->errmsg = '';
			$this->pageindex = 1;
			if ($this->action == "ok")
			{
				$sql = "select email,suspend from BCTable ";
				$sql.= " where kodebc = " . $this->queryvalue($this->param["userid"]);
				$sql.= " and email = " . $this->queryvalue($this->param["email"]);
				
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
						$newpass = $this->getRandomPassword();
						
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
					$this->errmsg = 'Kode BC dan/atau email yang anda masukkan salah';
				}
				$rs->close();
			}
		}		
	}
?>