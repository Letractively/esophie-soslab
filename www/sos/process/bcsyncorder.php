<?
	include_once "process/bcvieworder.php" ;
	
	class bcsyncorder extends bcvieworder
	{	
		function run() 
		{	
			parent::run();	

			if (!isset($this->param['salesid']) || $this->param['salesid'] == '')
				$this->gotopage('onlineorder');

			$this->loaddata();
		}		
	}
?>