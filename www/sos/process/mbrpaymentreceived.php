<?
	class mbrpaymentreceived extends controller
	{
		var $salesid;
                var $success;
                var $orderdate;
                var $totalbayar;
                var $status;
                var $paymdate;
                var $paymref;
	
		function run()
		{
			// no login required for callback
                        $this->checklogin = false;
                    
                        parent::run ();
                        
                        $this->checksalesid();
			$this->salesid = $this->param['salesid'];
                        
                        switch($this->action)
			{	
				case "success":
					$this->callback_success();
					break;
				case "failure":
					$this->callback_failure();
					break;
				case "none":
					$this->load();
					break;
			}
		}
                
                function callback_success()
                {
                    $this->updatesalesstatus($this->salesid, $this->sysparam['salesstatus']['confirmed']);
                    $this->gotopage('confirm', 'action=success');
                }
                
                function callback_failure()
                {
                    $this->gotopage('confirm', 'action=failure');
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
                                
                                if ($rs->value('status') == $this->sysparam['salesstatus']['confirmed'])
                                    $this->success = true;
                                else $this->success = false;
			}
			else
			{
				$rs->close();
				$this->gotohomepage();
			}
			$rs->close();
		}
	}
?>
