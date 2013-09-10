<?
	class mbrlogout extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			session_destroy();
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/member/logout";
                        $this->gatitle = "Member - Log out";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			$this->gotopage('login');
		}		
	}
?>