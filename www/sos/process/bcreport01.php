<?
	class bcreport01 extends rptcontroller
	{			
		var $searchcriteria;
		
		function run() 
		{				
			parent::run();
			switch($this->action)
			{
				case "none":
					$this->sortby = "itemid";
					break;
				case "reset":
					unset($this->param);
					break;
			}
						
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_report01 ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  
			if ( isset($this->param["search_kodemember"]) && trim($this->param["search_kodemember"]) != "" )
			{
				$sql.= " and kodemember = " . $this->queryvalue($this->param["search_kodemember"]);
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_kodemember:". $this->param["search_kodemember"];
			}
			if ( isset($this->param["search_status"]) && trim($this->param["search_status"]) != "" )
			{
				$sql.= " and status in (" . $this->param["search_status"] . ")";
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_status:". $this->param["search_status"];
			}		
			if ( $this->sortby == "" )	
				$this->sortby = "itemid";
			if ( $this->sortorder == "" )	
				$this->sortorder = "asc";
				
			$sql.= " order by " . $this->sortby . " " .$this->sortorder ;
			if ($this->debug()) echo $sql;
			$rs = $this->db->query($sql);
			$count = 0;
			
			while($rs->fetch())
			{			
				$this->items[$count]['itemid'] = $rs->value('itemid');
				$this->items[$count]['itemname'] = $rs->value('itemname');
				$this->items[$count]['qtybc'] = $rs->value('qtybc');
				$this->items[$count]['salesid'] = $rs->value('salesid');
				$this->items[$count]['kodemember'] = $rs->value('kodemember');
				$this->items[$count]['namamember'] = $rs->value('namamember');
				$this->items[$count]['statusname'] = $rs->value('statusname');
				$this->items[$count]['status'] = $rs->value('status');
				$count++;				
			}
			$rs->close();
		}
		
		
	}
?>