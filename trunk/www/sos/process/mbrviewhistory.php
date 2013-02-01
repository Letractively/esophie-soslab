<?
	class mbrviewhistory extends controller
	{	
		var $orderhistory;
		var $mbrno;
		var $mbrname;
		
		function run() 
		{	
			parent::run();
			switch($this->action)
			{			
				case "memberinfo" :
					$this->gotopage('memberinfo');
					break;
				case "none" :
					break;
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select top 1 * from vw_member where KodeMember = " . $this->queryvalue($this->userid());
			$rs = $this->db->query($sql);	
			if ($rs->fetch())
			{
				$this->mbrname 		= $rs->value('namaMember'); 
				$this->mbrno 		= $rs->value('KodeMember');
			}
			$rs->close();
			
			$sql = "select distinct top 3 * from vw_salestable where kodemember = " . $this->queryvalue($this->userid()) . " order by orderdate desc";
			
			$rs = $this->db->query($sql);			
			while ($rs->fetch()) 
			{					
				$order['salesid'] = $rs->value("salesid");
				$order['orderdate'] = $this->valuedatetime($rs->value("orderdate"));
				$order['bcid'] = $rs->value("kodebc");
				$order['total'] = $rs->value("totalbayar");
				$order['status'] = $rs->value("userstatus");
				$this->orderhistory[] = $order;	
			}
			$rs->close();
		}

	}
?>