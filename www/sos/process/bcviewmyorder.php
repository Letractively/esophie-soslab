<?
	class bcviewmyorder extends controller
	{
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $orderdate;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $includeppn;
		var $purchid;
		var $salesidsmi;
		var $items;
		var $status;
		
		function run() 
		{
			parent::run();			
			
			if (!isset($this->param['purchid']) || $this->param['purchid'] == '')
				$this->gotopage('onlineorder');
			switch($this->action)
			{
				case "cancel":
					$this->cancel();
					break;
				case "bcorder":
					$this->bcorder();
					break;
			}
			$this->purchid = $this->param['purchid'];
			$this->loaddata();
		}
		
		function loaddata() 
		{
			$sql = "select * from vw_purchtable where purchid = " . $this->queryvalue($this->purchid);
			$sql.= " and kodebc = " . $this->queryvalue($this->userid());
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				$this->salesidsmi = $rs->value('salesidsmi');
				$this->orderdate = $this->valuedatetime($rs->value('orderdate'));				
				
				$this->totalorder = $rs->value('totalorder'); 
				$this->discount = $rs->value('discount'); 
				$this->totalbayar = $rs->value('totalbayar'); 
				$this->includeppn = $rs->value('includeppn');
				
				$this->status = $rs->value('status'); 
				
				$sql = "select * from vw_purchline where purchid = " . $this->queryvalue($this->purchid);
				
				$rs1 = $this->db->query($sql);			
				$i = 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] = $rs1->value('itemid');
					$this->items[$i]['itemname'] = $rs1->value('itemname');
					$this->items[$i]['price'] = $rs1->value('price');
					$this->items[$i]['pricebc'] = $rs1->value('pricebc');
					$this->items[$i]['qty'] = $rs1->value('qty');
					$this->items[$i]['totalorder'] = $rs1->value('totalorder');
					$i++;
				}
				$rs1->close();			
				
			}
			$rs->close();
		}
		
		function cancel()
		{
			switch($this->param['backpage'])
			{
				case '1' :
					$this->gotopage('vieworder','salesid='.urlencode($this->param['purchid']));
					break;
				case '2' :
					$searchvalue = $this->param['sc'];
					$searchvalue = str_replace(";","&",str_replace(":", "=", $searchvalue));
					$this->gotopage('report2', ($searchvalue == "" ? "" : $searchvalue . "&pageaction=search") );
					break;
				case '3' :
					$searchvalue = $this->param['sc'];
					$searchvalue = str_replace(";","&",str_replace(":", "=", $searchvalue));
					$this->gotopage('report3', ($searchvalue == "" ? "" : $searchvalue . "&pageaction=search") );
					break;
				default:
					$this->gotopage('onlineorder');
			}
			
		}
		
		function bcorder()
		{
			$this->gotopage('vieworder', 'salesid='.$this->param['purchid']);
		}


	}
?>