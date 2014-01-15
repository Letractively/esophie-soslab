<?
	class mbrviewhistory extends controller
	{	
		var $orderhistory;
		var $lastorderstatus;
		
		function run() 
		{	
                        parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/member/homepage";
                        $this->gatitle = "Order - Member - Homepage";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			switch($this->action)
			{			
				case "place" :
                                    // Place new order                                   
                                    if (isset($this->param['salesid']) && strlen($this->param['salesid']) > 0 )
                                    {
                                        // Check if order is still on status openorder
                                        $sql = 'select count(*) from salestable ';
                                        $sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
                                        $sql.= ' and status = ' . $this->sysparam['salesstatus']['openorder'];
                                        $sql.= ' and salesid = ' . $this->queryvalue($this->param['salesid']);

                                        if($this->db->executeScalar($sql)) 
                                        {
                                            $this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['ordered']);
                                            $this->gaecommerce = $this->gaecommerce($this->param['salesid']);
                                        }
                                    }
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