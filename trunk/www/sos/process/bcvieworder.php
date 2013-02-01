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
		var $totalorderbc;
		var $discountbc;
		var $totalbayarbc;
		var $items;
		var $errmsg;
		var $pageview;
		
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
				case "ok":
					$this->refresh();
					$this->nextpage();
					break;
				case "delivered":	
					$this->delivered();
					break;
				case "clear":	
					$this->clearorder();
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
				$this->orderdate 			= $this->valuedatetime($rs->value('orderdate')); 
				$this->userstatus			= $rs->value('userstatus');
				$this->userstatusinfo			= $rs->value('statusinfo'); 
				$this->status				= $rs->value('status'); 
				$this->totalorder 			= $rs->value('totalorder'); 
				$this->discount 			= $rs->value('discount'); 
				$this->totalbayar 			= $rs->value('totalbayar');
				$this->paymentcharge 		= $rs->value('paymentcharge'); 
				$this->paymentname 			= $rs->value('paymentname'); 
				$this->totalorderbc 		= $rs->value('totalorderbc'); 
				$this->discountbc 			= $rs->value('discountbc'); 
				$this->totalbayarbc 		= $rs->value('totalbayarbc'); 
				
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->param['salesid']);
				$rs1 = $this->db->query($sql);			
				$i = 0;
				$this->totalorderedited 	= 0;
				$this->discountedited 		= 0;
				$this->totalbayaredited 	= $this->paymentcharge;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] 				= $rs1->value('itemid');
					$this->items[$i]['itemname'] 			= $rs1->value('itemname');
					$this->items[$i]['price'] 				= $rs1->value('price');
					$this->items[$i]['salesqty'] 			= $rs1->value('qty');
					$this->items[$i]['totalbayarmember']	= $rs1->value('totalbayar');
					$this->items[$i]['qtybc']				= $rs1->value('qtybc');
					$this->items[$i]['purchqty'] 			= $rs1->value('purchqty');
					$this->items[$i]['shortageqty'] 		= $rs1->value('shortageqty');					
					$this->items[$i]['totalbayarbc'] 		= $rs1->value('totalbayarbc');
					
					if ($rs1->value('shortageqty') > 0)
					{
						$this->totalorderedited 	+= $rs1->value('totalorderedited'); 
						$this->discountedited 		+= $rs1->value('discountedited'); 
						$this->totalbayaredited 	+= $rs1->value('totalbayaredited');
					}
					else
					{	
						$this->totalorderedited 	+= $rs1->value('totalorder'); 
						$this->discountedited 		+= $rs1->value('discount'); 
						$this->totalbayaredited 	+= $rs1->value('totalbayar');
					}
					$i++;
				}
				$rs1->close();				
			} else {
				$rs->close();
				$this->gotopage('onlineorder');
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
			$this->gotopage('onlineorder');
		}
		
		function nextpage()
		{
			$sql = "select count(*) as Total from vw_salesline ";
			$sql.= " where salesid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and shortageqty > 0 ";
			
			if ($this->db->executescalar($sql))
			{
				$this->gotopage('editorder','salesid='.urlencode($this->param['salesid']));
			}
			else
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
		
		function delivered()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['delivered']);
		}
		
		function clearorder()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['clear']);
			$this->gotopage('onlineorder');
		}
	}
?>