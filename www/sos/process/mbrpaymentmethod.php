<?
	class mbrpaymentmethod extends controller
	{	
		var $salesid;
		var $items;
		var $pageview;
		
		function run() 
		{	
			parent::run();	
			$this->salesid = $this->salesid();
			switch($this->action)
			{
				case "confirm":
					$this->confirm();
					break;
				case "none":					
					$laststatus = $this->getlaststatus();
					if ($laststatus != $this->sysparam['salesstatus']['openorder'])
						$this->gotolastpage($laststatus);
					break;
			}
			$this->load();
		}		
		
		function load()
		{
			$sql = "select paymentmode, name, description, isnull(inputMobileNumber,0) as inputmobilenumber  from paymentMode with (NOLOCK) order by seqno";
			$rs = $this->db->query($sql);			
			$i = 0;
			while ($rs->fetch()) 
			{
				$this->items[$i]["paymentmode"] = $rs->value('paymentmode');
				$this->items[$i]["name"] = $rs->value('name');
				$this->items[$i]["description"] = $rs->value('description');
				$this->items[$i]["mobilenumber"] = $rs->value('inputmobilenumber');
				$i++;
			}
		}
	
		function confirm()
		{	
			if (isset($this->param["mop"]))
			{				
				$sql = " exec sp_updatePaymentMode " . $this->queryvalue($this->salesid) . "," . $this->queryvalue($this->param["mop"]) . "," . $this->queryvalue($this->param["mobilenumber"]);
				echo $sql;
				$this->db->execute($sql);	
				$this->gotopage("confirm");
			}					
		}
	}
?>