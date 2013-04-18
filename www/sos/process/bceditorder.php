<?
	class bceditorder extends controller
	{	
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $mbrno;
		var $mbrname;
		var $mbraddress;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $paymentcharge;
		var $paymentname;
		var $status;
		var $orderdate;
		var $items;
		
		function run() 
		{	
			parent::run();	
			
			if (!isset($this->param['salesid']) || $this->param['salesid'] == '')
				$this->gotopage('onlineorder');
				
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
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and kodebc = " .  $this->queryvalue($this->userid());
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->mbrno = $rs->value('kodemember'); 
				$this->mbrname = $rs->value('namamember'); 
				$this->mbraddress = $rs->value('alamat'); 
				
				$this->paymentcharge 	= $rs->value('paymentcharge'); 
				$this->paymentname 		= $rs->value('paymentname'); 
				
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				
				$this->orderdate 			= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 				= $rs->value('userstatus'); 
				
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->param['salesid']);
				
				$rs1 = $this->db->query($sql);			
				$i = 0;
				$this->totalorder 		= 0;
				$this->discount 		= 0;
				$this->totalbayar 		= 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] 		= $rs1->value('itemid');
					$this->items[$i]['itemname'] 	= $rs1->value('itemname');
					$this->items[$i]['price'] 		= $rs1->value('price');
					$this->items[$i]['qty'] 		= $rs1->value('qtyedited');
					$this->items[$i]['totalorder'] 	= $rs1->value('totalorderedited');
					
					$this->totalorder 		+= $rs1->value('totalorderedited'); 
					$this->discount 		+= $rs1->value('discountedited'); 
					$this->totalbayar 		+= $rs1->value('totalbayaredited'); 
					
					$i++;
				}
				$this->totalbayar += $this->paymentcharge;
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
			$sql = "select count(*) as total from PurchLine where purchid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and qty > 0";

			if ($this->db->executescalar($sql))
			{
				$this->gotopage('ordertambahan','salesid='.urlencode($this->param['salesid']));
			}
			else
			{
				$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['validated']);
				$this->gotopage('syncorder','salesid='.urlencode($this->param['salesid']));
			}		
		}
	}
?>