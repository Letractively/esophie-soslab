<?
	class bcvieworder extends controller
	{	
		var $mbrno;
		var $mbrname;
		var $mbrmobile;
		var $mbraddress;
		var $status;
		var $userstatus;
		var $userstatusinfo;
		var $orderdate;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $totalorderedited;
		var $discountedited;
		var $totalbayaredited;
		var $paymentcharge;
		var $paymentname;
		var $createddate;
		var $totalorderbc;
		var $discountbc;
		var $totalbayarbc;
		var $items;
		var $errmsg;
		var $pageview;
		var $productrevisi;
		
		function run() 
		{
			parent::run();	

			if (!isset($this->param['salesid']) || $this->param['salesid'] == '')
				$this->gotopage('onlineorder');
				
			switch($this->action)
			{
				case "refresh":	
					$this->refresh();
					break;
				case "cancel":
					$this->cancel();
					break;
				case "validasi":
					$this->refresh();
					$this->nextpage();
					break;
				case "delivered":	
					$this->delivered();
					break;
				case "clear":	
					$this->clearorder();
					break;
				case "ready":	
					$this->setasready();
					break;
				case "none" :	
					$sql = "select status from salestable with (nolock) where salesid = " . $this->queryvalue($this->param['salesid']);					
					if ($this->db->executescalar($sql) == $this->sysparam['salesstatus']['ordered'])
					{
						$this->createPurchTable();
					}	
					break;
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_salestable ";
			$sql.= " where salesid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and kodebc = " . $this->queryvalue($this->userid());
							   
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->mbrno 				= $rs->value('kodemember'); 
				$this->mbrname 				= $rs->value('namamember');
				$this->mbrmobile 			= $rs->value('telp');
				$this->mbraddress			= $rs->value('alamat'); 
				if (is_null($rs->value('orderdate')))
					$this->orderdate 			= $this->valuedatetime($rs->value('createddate')); 
				else
					$this->orderdate 			= $this->valuedatetime($rs->value('orderdate')); 
					
				$this->userstatus			= $rs->value('userstatus');
				$this->userstatusinfo		= $rs->value('statusinfo'); 
				$this->status				= $rs->value('status'); 
				$this->totalorder 			= $rs->value('totalorder'); 
				$this->discount 			= $rs->value('discount'); 
				$this->totalbayar 			= $rs->value('totalorder') + $rs->value('discount'); //$rs->value('totalbayar');
				$this->paymentcharge 		= $rs->value('paymentcharge'); 
				$this->paymentname 			= $rs->value('paymentname'); 
				$this->totalorderbc 		= $rs->value('totalorderbc'); 
				$this->discountbc 			= $rs->value('discountbc'); 
				$this->totalbayarbc 		= $rs->value('totalbayarbc'); 
				
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->param['salesid']);
				$rs1 = $this->db->query($sql);			
				$i = 0;
				$this->productrevisi = '';
				if ($this->status == $this->sysparam['salesstatus']['edited'] ||
					$this->status == $this->sysparam['salesstatus']['ordered'])
				{
					$this->totalorder 			= 0;
					$this->discount 			= 0;
					$this->totalbayar 			= 0;
				}				
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] 				= $rs1->value('itemid');
					$this->items[$i]['itemname'] 			= $rs1->value('itemname');
					$this->items[$i]['price'] 				= $rs1->value('price');
					$this->items[$i]['salesqty'] 			= $rs1->value('qty');
					if ($this->status == $this->sysparam['salesstatus']['edited'] ||
					    $this->status == $this->sysparam['salesstatus']['ordered'])
						$this->items[$i]['totalordermember']	= $rs1->value('totalorderedited');
					else
						$this->items[$i]['totalordermember']	= $rs1->value('totalorder');
					
					$this->items[$i]['totalbayarmember']	= $rs1->value('totalbayar');
					$this->items[$i]['qtybc']				= $rs1->value('qtybc');
					$this->items[$i]['purchqty'] 			= $rs1->value('purchqty');
					$this->items[$i]['shortageqty'] 		= $rs1->value('shortageqty');
					$this->items[$i]['totalorderbc'] 		= $rs1->value('totalorderbc');					
					$this->items[$i]['totalbayarbc'] 		= $rs1->value('totalbayarbc');
					
					if ($this->items[$i]['salesqty'] > $this->items[$i]['purchqty'] + $this->items[$i]['qtybc'])
					{
						$this->productrevisi .= ($this->productrevisi != ''? ', ' : '') . $this->items[$i]['itemid'];
					}
					
					if ($this->status == $this->sysparam['salesstatus']['edited'] ||
					    $this->status == $this->sysparam['salesstatus']['ordered'])
					{
						$this->totalorder 	+= $rs1->value('totalorderedited'); 
						$this->discount 	+= $rs1->value('discountedited'); 
						$this->totalbayar 	+= $rs1->value('totalbayaredited');					
					}
					$i++;
				}
				$rs1->close();				
			} else {
				$rs->close();
				$this->gotopage('onlineorder');
			}
			$rs->close();
			
			$sql = "select * from vw_purchtable ";
			$sql.= " where purchid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and kodebc = " . $this->queryvalue($this->userid());
							   
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				
			}
			$rs->close();
			
		}

		function createPurchTable()
		{			
			$sql = "if not exists(select purchid from PurchTable where PurchId = " . $this->queryvalue($this->param['salesid']) . ")";
			$sql.= " insert into PurchTable (purchid, kodebc, orderdate, status) values (" . $this->queryvalue($this->param['salesid']);
			$sql.= "," . $this->queryvalue($this->userid()) . ",getdate(),1)" ;
			$this->db->execute($sql);
			
			$this->updatePurchLine();
		}
		
		function updatePurchLine()
		{
			$sql = "exec sp_updatePurchLine " . $this->queryvalue($this->param['salesid']);
			$this->db->execute($sql);
			
			$sql = "exec sp_updatepurchtotal " . $this->queryvalue($this->param['salesid']);
			$this->db->execute($sql);			
		}
		
		function refresh()
		{
			if (!isset($this->param['itemid'])) return;
			for($i=0;$i<count($this->param['itemid']);$i++)
			{
				$qty = is_numeric($this->param["itemqty"][$i]) ? $this->param["itemqty"][$i] : "0";
				
				if ( $qty > 0 )
				{
					$sql = "update salesline set qtybc = case when " . $qty ;
					$sql.= " > qty then qty else " . $qty . " end ";
					$sql.= " where salesid = " . $this->queryvalue($this->param['salesid']);
					$sql.= " and itemid = " . $this->queryvalue($this->param['itemid'][$i]);
					//echo $sql . "<bR><Br>";
					$this->db->execute($sql);
				}
			}
			$this->updatePurchLine();
		}
		
		function cancel()
		{
			switch($this->param['backpage'])
			{
				case '2' :
					$this->gotopage('report2');
					break;
				default:
					$this->gotopage('onlineorder');
			}
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
				$this->gotopage('onlineorder');
			}
		}
		
		function delivered()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['delivered']);
		}
		
		function setasready()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['ready']);
		}
		
		function clearorder()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['clear']);
			$this->gotopage('onlineorder');
		}
	}
?>