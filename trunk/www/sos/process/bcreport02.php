<?
	class bcreport02 extends rptcontroller
	{	
	
		var $searchcriteria;
		
		function run() 
		{				
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/report/orderhistory";
                        $this->gatitle = "Order - BC - Report Order History";
                        // GOOGLE ANALYTICS PAGE TRACKING
			
			switch($this->action)
			{
				case "none":
					$this->sortby = "salesid";
					$this->loaddata();
					break;
				case "reset":
					unset($this->param);
					break;
				default:
					$this->loaddata();
			}			
		}		
		
		function loaddata() 
		{
			$this->searchcriteria = "";
			
			$sql = "select * from vw_report02 ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  			
			if ( isset($this->param["search_salesid"]) && trim($this->param["search_salesid"]) != "" ) 
			{
				$sql.= " and salesid = " . $this->queryvalue($this->param["search_salesid"]);
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_salesid:". $this->param["search_salesid"];
			}
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
			if ( $this->action == "none" )
			{
				$sql.= " and status in (0,2,3,4,5,6,7,8,9,10)";
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_status:0,2,3,4,5,6,7,8,9,10";
			}
			if ( isset($this->param["search_salesidsmi"]) && trim($this->param["search_salesidsmi"]) != "" )
			{
				$sql.= " and salesidsmi = " . $this->queryvalue($this->param["search_salesidsmi"]);
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_salesidsmi:". $this->param["search_salesidsmi"];
			}
			if ( isset($this->param["search_orderdate_from"]) && trim($this->param["search_orderdate_from"]) != "")
			{
				$sql.= " and DATEADD(dd, DATEDIFF(dd, 0, orderdate), 0) >= " . $this->querydatevalue($this->param["search_orderdate_from"]);
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_orderdate_from:". $this->param["search_orderdate_from"];
			}
			if ( isset($this->param["search_orderdate_to"]) && trim($this->param["search_orderdate_to"]) != "")
			{
				$sql.= " and DATEADD(dd, DATEDIFF(dd, 0, orderdate), 0) <= " . $this->querydatevalue($this->param["search_orderdate_to"]);
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_orderdate_to:". $this->param["search_orderdate_to"];
			}
			if ( $this->action == "none" )
			{
				$sql.= " and orderdate >= DateAdd(Day,-7,DATEADD(dd, DATEDIFF(dd, 0, getdate()), 0))";
				$date0 = strtotime ( '+0 days' ) ;
				$date0 = date ( 'd/m/Y' , $date0 );
				$date7 = strtotime ( '-7 days' ) ;
				$date7 = date ( 'd/m/Y' , $date7 );
				$this->searchcriteria .= ($this->searchcriteria != "" ? ";" : "") . "search_orderdate_from:" . $date7;
				
				$this->param['search_orderdate_from'] = $date7 ;
				$this->param['search_orderdate_to'] = $date0 ;
			}
			
			if ( $this->sortby == "" )	
				$this->sortby = "salesid";
			if ( $this->sortorder == "" )	
				$this->sortorder = "asc";
			$sql.= " order by " . $this->sortby . " " .$this->sortorder ;

			//echo $sql;
			
			$rs = $this->db->query($sql);
			$count = 0;
			
			while($rs->fetch())
			{	
				$this->items[$count]['salesid'] = $rs->value('salesid');
				$this->items[$count]['orderdate'] = (is_null($rs->value('orderdate')) ? '-' : $this->valuedatetime($rs->value('orderdate')));
				$this->items[$count]['kodemember'] = $rs->value('kodemember');
				$this->items[$count]['namamember'] = $rs->value('namamember');
				$this->items[$count]['totalbayar'] = $rs->value('totalbayar');
				$this->items[$count]['salesidsmi'] = $rs->value('salesidsmi');
				$this->items[$count]['statusname'] = $rs->value('statusname');
				$this->items[$count]['status'] = $rs->value('status');
				$count++;
			}
			$rs->close();
		}
		
		
	}
?>