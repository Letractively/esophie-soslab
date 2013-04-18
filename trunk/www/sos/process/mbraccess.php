<?
	class mbraccess extends controller
	{	
		function run() 
		{	
			$this->checklogin = false;
			$this->disclaimercheck = false;
			
			$userid = '6000825463'; //testing purpose only
			if (isset($_GET['userid'])) $userid = $_GET['userid'];
			$_SESSION[$this->sysparam['session']['smuserid']] = $userid;
			
			parent::run();

			echo $_SESSION[$this->sysparam['session']['smuserid']];
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