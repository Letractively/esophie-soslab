<?
	class bclogin extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
                        
			parent::run();
                        
                        if ($this->login()) $this->gotopage('onlineorder');
			
			if ($this->action == "ok")
			{
				$userarray = explode('@',$this->param["userid"], 2);
                                if ($userarray && sizeof($userarray) > 0)
                                {
                                    $userid = $userarray[0];
                                    $adminUser = (sizeof($userarray) > 1) ? $userarray[1] : "";
                                }

                                $sql = "select kodebc, suspend from BCTable ";
                                $sql.= " where kodebc = " . $this->queryvalue($userid);
                                if (strlen($adminUser) > 0) 
                                {
                                    $sql.= " and exists (select 1 from BCTable ";
                                    $sql.= " where kodebc = ". $this->queryvalue($adminUser) . " and password = " . $this->queryvalue(md5($this->param["password"])) . ")";
                                }
                                else
                                {
                                    $sql.= " and password = " . $this->queryvalue(md5($this->param["password"]));
                                }
                                $rs = $this->db->query($sql);
                                if ($rs->fetch())
                                {	
                                        if ($rs->value('suspend') == 1)
                                        {
                                                $this->errmsg = $this->sysparam['appmsg']['bcaccountsuspend'];
                                        }
                                        else
                                        {
                                                $_SESSION[$this->sysparam['session']['userid']] = $userid;
                                                $_SESSION[$this->sysparam['session']['usertype']] = 2;
                                                $rs->close();
                                                $this->gotopage('onlineorder');
                                        }
                                }
                                else
                                {
                                        $this->errmsg = 'Kode BC dan/atau password yang anda masukkan salah';
                                }
                                $rs->close();
			}
		}		
	}
?>