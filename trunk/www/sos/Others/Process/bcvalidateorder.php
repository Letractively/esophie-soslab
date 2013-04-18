<?
	class bcvalidateorder extends controller
	{	
		var $mbrno;
		var $mbrname;
		var $status;
		var $orderdate;
		var $totalorder;
		var $discount;
		var $paymentcharge;
		var $paymentname;
		var $totalbayar;
		var $totalbayarbc;
		var $items;
		var $errmsg;
		
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
				case "none":
					$this->createPurchTable();
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_salestable ";
			$sql.= " where salesid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and status = " . $this->sysparam['salesstatus']['ordered'];
			$sql.= " and kodebc = " . $this->queryvalue($this->userid());
							   
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->mbrno 				= $rs->value('kodemember'); 
				$this->mbrname 				= $rs->value('namamember'); 
				$this->orderdate 			= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 				= $rs->value('userstatus'); 
				$this->totalorder 			= $rs->value('totalorder'); 
				$this->discount 			= $rs->value('discount'); 
				$this->totalbayar 			= $rs->value('totalbayar');
				$this->paymentcharge 		= $rs->value('paymentcharge'); 
				$this->paymentname 			= $rs->value('paymentname'); 
				$this->totalbayarbc 		= $rs->value('totalbayarbc'); 
								
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->param['salesid']);
				$rs1 = $this->db->query($sql);			
				$i = 0;
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
			
			$sql = "exec sp_updatepurchtotal " . $this->queryvalue($this->param['salesid']) . "," . $this->sysparam['app']['bcincludeppn'];
			$this->db->execute($sql);
			
			$sql = "exec sp_updatesalestotaledited " . $this->queryvalue($this->param['salesid']);
			$this->db->execute($sql);
		}
		
		function refresh()
		{
			if (!isset($this->param['itemid'])) return;
			for($i=0;$i<count($this->param['itemid']);$i++)
			{
				$qty = is_numeric($this->param["itemqty"][$i]) ? $this->param["itemqty"][$i] : "0";
				$sql = "update salesline set qtybc = case when " . $qty ;
				$sql.= " > qty then qty else " . $qty . " end ";
				$sql.= " where salesid = " . $this->queryvalue($this->param['salesid']);
				$sql.= " and itemid = " . $this->queryvalue($this->param['itemid'][$i]);
				//echo $sql . "<bR><Br>";
				$this->db->execute($sql);				
			}
			$this->updatePurchLine();
		}
		
		function cancel()
		{
			//need delete the qty bc or not?
			$this->gotopage('onlineorder');
		}
		
		function nextpage()
		{
			$sql = "select count(*) as Total from vw_validateorderd ";
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
					$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['inprogress']);
					$this->gotopage('onlineorder');
				}
			}			
		}
	}
?>