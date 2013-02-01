<?
	class bcreport03 extends rptcontroller
	{			
		function run() 
		{				
			parent::run();
			switch($this->action)
			{
				case "none":
					$this->sortby = "salesid";
					break;
				case "reset":
					unset($this->param);
					break;
			}
						
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_report03 ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  			
			$sql.= ((isset($this->param["search_salesid"]) && trim($this->param["search_salesid"]) != "") ? " and salesid = " . $this->queryvalue($this->param["search_salesid"]) : "" );
			//$sql.= ((isset($this->param["search_kodemember"]) && trim($this->param["search_kodemember"]) != "") ? " and kodemember = " . $this->queryvalue($this->param["search_kodemember"]) : "" );			
			//$sql.= ((isset($this->param["search_namamember"]) && trim($this->param["search_namamember"]) != "") ? " and namamember like '%" . str_replace("'","''",$this->param["search_namamember"]) . "%'" : "" );			
			$sql.= ((isset($this->param["search_status"]) && trim($this->param["search_status"]) != "") ? " and status = " . $this->param["search_status"] : "" );
			$sql.= ((isset($this->param["search_salesidsmi"]) && trim($this->param["search_salesidsmi"]) != "") ? " and salesidsmi = " . $this->queryvalue($this->param["search_salesidsmi"]) : "" );
			if (isset($this->param["search_orderdate_from"]) && trim($this->param["search_orderdate_from"]) != "")
				$sql.= " and orderdate >= " . $this->querydatevalue($this->param["search_orderdate_from"]);
			if (isset($this->param["search_orderdate_from"]) && trim($this->param["search_orderdate_to"]) != "")
				$sql.= " and orderdate >= " . $this->querydatevalue($this->param["search_orderdate_to"]);
								
			$sql.= " order by " . $this->sortby . " " .$this->sortorder ;

			//echo $sql;
			
			$rs = $this->db->query($sql);
			$count = 0;
			
			while($rs->fetch())
			{			
				$this->items[$count]['salesid'] = $rs->value('salesid');
				$this->items[$count]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
				$this->items[$count]['kodemember'] = $rs->value('kodemember');
				//$this->items[$count]['namamember'] = $rs->value('namamember');
				$this->items[$count]['totalbayarmbr'] = $rs->value('totalbayarmbr');
				$this->items[$count]['salesidsmi'] = $rs->value('salesidsmi');
				$this->items[$count]['paymentcharge'] = $rs->value('paymentcharge');
				$this->items[$count]['totalbayarbc'] = $rs->value('totalbayarbc');
				$this->items[$count]['kreditbc'] = $rs->value('kreditbc');
				$this->items[$count]['statusname'] = $rs->value('statusname');
				$count++;				
			}
			$rs->close();
		}
		
		
	}
?>