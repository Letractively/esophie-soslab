<?
	class bcreport01 extends rptcontroller
	{			
		function run() 
		{				
			parent::run();
			switch($this->action)
			{
				case "none":
					$this->sortby = "itemid";
					break;
			}
						
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_report01 ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  
			$sql.= " order by " . $this->sortby . " " .$this->sortorder ;

			$rs = $this->db->query($sql);
			$count = 0;
			
			while($rs->fetch())
			{			
				$this->items[$count]['itemid'] = $rs->value('itemid');
				$this->items[$count]['itemname'] = $rs->value('itemname');
				$this->items[$count]['qtybc'] = $rs->value('qtybc');
				$this->items[$count]['salesid'] = $rs->value('salesid');
				$this->items[$count]['kodemember'] = $rs->value('kodemember');
				$this->items[$count]['status'] = $rs->value('status');
				$count++;				
			}
			$rs->close();
		}
		
		
	}
?>