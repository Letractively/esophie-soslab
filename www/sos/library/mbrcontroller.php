<?
	include_once "database.php";
	include_once "maincontroller.php";
	
	class controller extends maincontroller
	{			
		function debug() { return false; }	
		function systemmaintenance() { return false; }	
		function setsysparam()
		{
			parent::setsysparam();
				
			$this->sysparam['memberstatus']['firstlogin']	= "firstlogin";
			$this->sysparam['memberstatus']['suspend']	= "suspend";
			$this->sysparam['memberstatus']['neworder']	= "neworder";			
		}
		
		function run()
		{
			$this->usertype = 1;
			parent::run();			
		}
		
		function salesid() 
		{ 
			//note: hanya ada 1 salesid active per member 			
			/*
			$sql = "select salesid from salesTable ". 
				   " where kodemember = " . $this->queryvalue($this->userid()) .
				   " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['paid']) .
				   " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['cancelled']) .
				   " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['delivered']);
			$ret = $this->db->executeScalar($sql);
			*/
			$ret = $_SESSION[$this->sysparam['session']['salesid']];
			return $ret;
		}
		
		function gotopage($page)
		{
			switch(strtolower($page))
			{
				case 'login' 			: header("location:" . $this->sysparam['app']['mbrurl']); break;
				case 'disclaimer' 		: header("location:mbrdisclaimer.php"); break;
				case 'suspend' 			: header("location:mbrsuspend.php"); break;
				case 'orderhistory' 		: header("location:mbrviewhistory.php"); break;
				case 'memberinfo' 		: header("location:mbrcekdata.php"); break;
				case 'selectbc' 		: header("location:mbrpilihbc.php"); break;	
				case 'inputitem' 		: header("location:mbrpilihitem.php"); break;				
				case 'checkitem' 		: header("location:mbrvieworder.php?edit=1"); break;
				case 'waiting' 			: header("location:mbrvieworder.php"); break;
				case 'confirm' 			: header("location:mbrvieworder.php"); break;
				case 'paymentmethod'		: header("location:mbrpaymentmethod.php"); break;
				case 'paymentconfirm' 		: header("location:mbrpaymentconfirm.php"); break;
				case 'paymentreceived' 		: header("location:mbrpaymentreceived.php"); break;
			}
		}
		
		function gotolastpage($status)
		{
			switch($status)
			{
				case $this->sysparam['memberstatus']['firstlogin']	: $this->gotopage('disclaimer'); break;
				case $this->sysparam['memberstatus']['suspend']		: $this->gotopage('suspend'); break;
				case $this->sysparam['salesstatus']['openorder']	: $this->gotopage('checkitem'); break;
				case $this->sysparam['salesstatus']['ordered']		:
				case $this->sysparam['salesstatus']['bypassed']		:
				case $this->sysparam['salesstatus']['inprogress']	: $this->gotopage('waiting'); break;
				case $this->sysparam['salesstatus']['edited']		: 
				case $this->sysparam['salesstatus']['validated']	: $this->gotopage('confirm'); break;
				case $this->sysparam['salesstatus']['confirmed'] 	: $this->gotopage('paymentreceived'); break;
				case $this->sysparam['memberstatus']['neworder'] 	: 
				case $this->sysparam['memberstatus']['cancelled'] 	:
				default							: $this->gotopage('orderhistory'); break;
			}
		}
		
		function getlaststatus()
		{
			$status = "";
			$sql = "select * from memberTable where kodemember = " . $this->queryvalue($this->userid());

			$_SESSION[$this->sysparam['session']['salesid']] = '';
			
			$rs = $this->db->query($sql);
			if ($rs->fetch())
			{				
				if ($rs->value("suspend") == 0)
				{
					if (is_null($rs->value("acceptdate")))
					{
						$status = $this->sysparam['memberstatus']['firstlogin'];
					}
					else
					{
						$sql = "select top 1 salesid, status from salesTable with (nolock) ";
						$sql.= " where kodemember = " . $this->queryvalue($this->userid());
						$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['paid']);
						$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['cancelled']);
						$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['delivered']);
						$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['clear']);
						
						$rs1 = $this->db->query($sql);
						if ($rs1->fetch())
						{
							$status = $rs1->value("status");
							$_SESSION[$this->sysparam['session']['salesid']] = $rs1->value("salesid");
						}
						else
						{
							$status = $this->sysparam['memberstatus']['neworder'];
						}
						$rs1->close();
					}
				}
				else
				{
					$status = $this->sysparam['memberstatus']['suspend'];
				}
			}
			else
			{
				$status = $this->sysparam['memberstatus']['firstlogin'];
			}
			
			$rs->close();

			return $status;
		}
		
	}
?>