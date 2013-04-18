<?	
	include_once "batchcontroller.php";
	
	class emailsmscontroller extends batchcontroller
	{		
		var $db;
		var $usertype;

		function __construct()
		{
			$this->setsysparam();
			$this->opendatabaseconnection();
		}		
	}
	
?>