<?
	class mbrdisclaimer extends controller
	{	
		var $disclaimer;
		var $errmsg;
		function run() 
		{						
			parent::run();					  
			switch($this->action)
			{			
				case "setuju" :
					if (isset($this->param["agree"]))
					{
						// Create or update the BC mapping for this new member
						$sql = "exec sp_sos_IMPORTMEMBER " . $this->queryvalue($this->userid());
						if ($this->debug()) echo $sql;
						$this->db->execute($sql);
						$this->gotopage('memberinfo');
					}
					else
					{
						$this->errmsg = "Silahkan click check box sebagai pernyataan setuju atas persyaratan Sophie Online Shopping.";
					}
					break;
				case "none" :
					$laststatus = $this->getlaststatus();
					if ($this->debug()) echo "laststatus:" .$laststatus;
					if ($laststatus != $this->sysparam['memberstatus']['firstlogin'])
						$this->gotolastpage($laststatus);
					break;
			}

			$this->disclaimer = $this->sysparam['app']['mbrdisclaimer'];
			
		}
		
		
		
	}
?>