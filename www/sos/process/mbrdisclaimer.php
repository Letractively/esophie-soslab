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
                                            $sql = "UPDATE memberTable SET acceptdate=GETDATE() WHERE kodemember = ". 
                                                    $this->queryvalue($this->userid());
                                            $this->db->execute($sql);
                                            $this->gotopage('inputitem');
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