<?
	class bcmyorder extends controller
	{	
		var $orders;
		
		function run() 
		{	
			parent::run();			
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_myonlineorder ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  
			$sql.= " order by orderdate";
			
			$rs = $this->db->query($sql);
			$countorders = 0;
			
			while($rs->fetch())
			{			
				$this->orders[$countorders]['purchid'] = $rs->value('purchid');
				$this->orders[$countorders]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
				$this->orders[$countorders]['salesidsmi'] = $rs->value('salesidsmi');
				$this->orders[$countorders]['totalbayar'] = $rs->value('totalbayar');
				$this->orders[$countorders]['status'] = $rs->value('status');
				$this->orders[$countorders]['userstatus'] = $rs->value('userstatus');
				$countorders++;
			}
			$rs->close();
		}
	}
?>