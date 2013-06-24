<?
	class mbrviewhistory extends controller
	{	
		var $orderhistory;
		var $lastorderstatus;
		
		function run() 
		{	
                        parent::run();
			switch($this->action)
			{			
				case "none" :
					break;
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select top 5 * from vw_salestable where kodemember = " . $this->queryvalue($this->userid());
			$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['clear']);
			$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
			$sql.= " order by CASE 
                                    WHEN status IN (1,2,3,4,5,6,7,8,9) THEN status
                                    WHEN status IN (0,10) THEN 10
                                    ELSE 11
                                END, salesid desc";
			
			$rs = $this->db->query($sql);			
			while ($rs->fetch()) 
			{						
				$order['salesid'] = $rs->value('salesid');
				$order['orderdate'] = (is_null($rs->value('orderdate')) ? '-' : $this->valuedatetime($rs->value('orderdate')));
				$order['bcid'] = $rs->value('kodebc');
				$order['total'] = $rs->value('totalbayar');
				$order['userstatus'] = $rs->value('userstatus');
				$order['status'] = $rs->value('status');
				$this->orderhistory[] = $order;	
			}
			$rs->close();

			$sql = "select top 1 salesid, status from vw_salestable where kodemember = " . $this->queryvalue($this->userid());
			$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['clear']);
			$sql.= " order by salesid desc";
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				if ($this->salesid == '')
				{
					$this->salesid = $rs->value('salesid');
					
				}
				$this->lastorderstatus = $rs->value('status');
			}
			$rs->close();
			$this->setmbrmsg();
		}
	}
?>