<?
	class mbrdisclaimer extends controller
	{	
		var $disclaimer;
		var $errmsg;
		var $firstlogin;
		
		function run() 
		{						
			$this->disclaimercheck = false;
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
						$this->gotopage('orderhistory');
					}
					else
					{
						$this->errmsg = "Silahkan click check box sebagai pernyataan setuju atas persyaratan Sophie Online Shopping.";
					}
					break;
				case "lanjut" :
					$this->gotopage('orderhistory');
					break;
				case "none" :
					$this->firstlogin = ($this->disclaimerchecking() == 'disclaimer');
					break;
			}

			$this->disclaimer = $this->sysparam['app']['mbrdisclaimer'];
			
		}
		
		
		
	}
?>