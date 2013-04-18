<?
	class mbraccess extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			$this->disclaimercheck = false;
			
			parent::run();
			
			$userid = '101'; //testing purpose only
			if (isset($_GET['userid'])) $userid = $_GET['userid'];
			$_SESSION[$this->sysparam['session']['smuserid']] = $userid;
			
			if (isset($_SESSION[$this->sysparam['session']['smuserid']]))
			{
				$_SESSION[$this->sysparam['session']['userid']] = $_SESSION[$this->sysparam['session']['smuserid']];
				$_SESSION[$this->sysparam['session']['usertype']] = 1;
				$this->gotopage('orderhistory');				
			}
			else
			{
				$this->gotopage('login');
			}
		}		
	}
?>