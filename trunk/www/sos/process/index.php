<?
	class index extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			parent::run();
			
			if ($this->action == "ok")
			{
				$sql = "select kodebc, suspend from BCTable ";
				$sql.= " where kodebc = " . $this->queryvalue($this->param["userid"]);
				$sql.= " and password = " . $this->queryvalue(md5($this->param["password"]));
				
				$rs = $this->db->query($sql);
				
				if ($rs->fetch())
				{	
					if ($rs->value('suspend') == 1)
					{
						$this->errmsg = $this->sysparam['appmsg']['bcaccountsuspend'];
					}
					else
					{
						$_SESSION[$this->sysparam['session']['userid']] = $this->param["userid"];
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