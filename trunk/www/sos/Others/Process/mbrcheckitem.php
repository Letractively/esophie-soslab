<?
	class mbrcheckitem extends controller
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
		var $salesid;
		var $items;
		
		function run() 
		{
			parent::run();			
			
			$this->salesid = $this->salesid();
			
			switch($this->action)
			{
				case "confirm":
					$this->confirm();				
					break;
				case "refresh":					
					$this->refresh();
					break;
				case "tambah":
					$this->gotopage('inputitem');
					break;
				case "none":					
					$laststatus = $this->getlaststatus();
					if ($laststatus != $this->sysparam['salesstatus']['openorder'])
						$this->gotolastpage($laststatus);
					break;
			}
			
						
			$this->loaddata();
		}
		
		function loaddata() 
		{
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->mbrno = $rs->value('kodemember'); 
				$this->mbrname = $rs->value('namamember'); 
				$this->mbraddress = $rs->value('alamat'); 
				
				$this->totalorder = $rs->value('totalorder'); 
				$this->discount = $rs->value('discount'); 
				$this->totalbayar = $rs->value('totalbayar'); 
				
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->salesid);
				
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
			$rs->close();
		}
		
		function refresh()
		{
			if (!isset($this->param['itemid'])) return;
			for($i=0;$i<count($this->param['itemid']);$i++)
			{
				$sql = "exec sp_updateSalesLine " . $this->queryvalue($this->salesid) . "," . $this->queryvalue($this->param['itemid'][$i]) . "," . $this->param["itemqty"][$i] . ",0";				
				$this->db->execute($sql);				
			}
			
			$sql = "exec sp_updateSalesTotal " . $this->queryvalue($this->salesid);					
			$this->db->execute($sql);	
		}
		
		function confirm()
		{
			//$this->updatesalesstatus($this->salesid,$this->sysparam['salesstatus']['ordered']);
			$this->refresh();
			$this->gotopage('paymentmethod');
		}
	}
?>