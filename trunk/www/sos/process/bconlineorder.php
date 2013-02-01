<?
	class bconlineorder extends controller
	{	
		var $neworders;
		var $pendingorders;
		var $inprogressorders;
		
		function run() 
		{	
			parent::run();			
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_onlineorder ";
			$sql.= " where kodebc = " . $this->queryvalue($this->userid());  
			$sql.= " and (cleardate is null AND Status != 10)";
			$sql.= " order by orderdate desc";
			
			$rs = $this->db->query($sql);
			$countneworder = 0;
			$countpendingorder = 0;
			$countinprogress = 0;
			
			while($rs->fetch())
			{			
				
				switch($rs->value('status'))
				{	
					case 2:
						$this->neworders[$countneworder]['salesid'] = $rs->value('salesid');
						$this->neworders[$countneworder]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
						$this->neworders[$countneworder]['kodemember'] = $rs->value('kodemember');
						$this->neworders[$countneworder]['totalbayar'] = $rs->value('totalbayar');
						$this->neworders[$countneworder]['timeleft'] = $rs->value('timeleft');
						$this->neworders[$countneworder]['statuscode'] = $rs->value('status');
						$countneworder++;
						break;
					case 4:
						$this->inprogressorders[$countinprogress]['salesid'] = $rs->value('salesid');
						$this->inprogressorders[$countinprogress]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
						$this->inprogressorders[$countinprogress]['kodemember'] = $rs->value('kodemember');
						$this->inprogressorders[$countinprogress]['totalbayar'] = $rs->value('totalbayar');
						$this->inprogressorders[$countinprogress]['syncstatus'] = $rs->value('syncstatus');
						$this->inprogressorders[$countinprogress]['statuscode'] = $rs->value('syncstatuscode');
						$countinprogress++;
						break;
					default:
						$this->pendingorders[$countpendingorder]['salesid'] = $rs->value('salesid');
						$this->pendingorders[$countpendingorder]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
						$this->pendingorders[$countpendingorder]['kodemember'] = $rs->value('kodemember');
						$this->pendingorders[$countpendingorder]['totalbayar'] = $rs->value('totalbayar');
						$this->pendingorders[$countpendingorder]['userstatus'] = $rs->value('userstatus');
						$this->pendingorders[$countpendingorder]['statuscode'] = $rs->value('status');
						$countpendingorder++;
						break;	
				}
			}
			$rs->close();
		}
	}
?>