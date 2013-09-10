<?
	class bclogout extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			session_destroy();
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/logout";
                        $this->gatitle = "BC - Logout page";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			$this->gotopage('login');
		}		
	}
?>