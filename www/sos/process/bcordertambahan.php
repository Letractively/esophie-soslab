<?
	class bcordertambahan extends controller
	{	
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $includeppn;
		var $items;
		
		function run() 
		{	
			parent::run();	
			
			//if (!isset($this->param['salesid']) || $this->param['salesid'] == '')
			//	$this->gotopage('onlineorder');
				
			switch($this->action)
			{
				case "ok":
					$this->nextpage();
					break;
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_purchtable where purchid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and kodebc = " .  $this->queryvalue($this->userid());
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				
				$this->totalorder = $rs->value('totalorder'); 
				$this->discount = $rs->value('discount'); 				
				$this->totalbayar = $rs->value('totalbayar'); 
				$this->includeppn = $rs->value('includeppn');
				
				$sql = "select * from vw_purchline where purchid = " . $this->queryvalue($this->param['salesid']);
				$sql.= " and qty > 0";
				$rs1 = $this->db->query($sql);			
				$i = 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] = $rs1->value('itemid');
					$this->items[$i]['itemname'] = $rs1->value('itemname');
					$this->items[$i]['price'] = $rs1->value('price');
					$this->items[$i]['qty'] = $rs1->value('qty');
					$this->items[$i]['totalorder'] = $rs1->value('totalorder');
					$i++;
				}
				$rs1->close();			
				
			}
			else
			{
				$rs->close();
				$this-gotopage('onlineorder');
			}
			$rs->close();
		}

		function nextpage()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['inprogress']);
			$this->gotopage('onlineorder');
		}
	}
?>