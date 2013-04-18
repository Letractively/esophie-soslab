<?
	class mbrpaymentreceived extends controller
	{
		var $salesid;
	
		function run()
		{
			parent::run ();
			$this->salesid = $this->salesid();
			$this->load();
		}
		
		function load()
		{
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{					
				$this->totalbayar 		= $rs->value('totalbayar'); 
				$this->orderdate 		= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 			= $rs->value('userstatus'); 
			}
			else
			{
				$rs->close();
				$this->gotopage('memberinfo');
			}
			$rs->close();
		}
	}
?>
