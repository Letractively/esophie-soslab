<?
	class bclogout extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			session_destroy();
			parent::run();
			$this->gotopage('login');
		}		
	}
?>