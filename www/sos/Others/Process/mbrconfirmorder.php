<?
	class mbrconfirmorder extends controller
	{	
		var $salesid;
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $mbrno;
		var $mbrname;
		var $mbraddress;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $status;
		var $orderdate;
		var $items;
		var $pageview;
		var $timeleft;
		
		function run() 
		{	
			parent::run();	
			$this->salesid = $this->salesid();
			switch($this->action)
			{
				case "sendordertobc":
					$this->sendordertobc();
					break;
				case "confirm":
					$this->confirm();
					break;
				case "cancel":
					$this->cancel();
					break;
				case "pembayaran":
					$this->pembayaran();
					break;
				case "none":					
					$laststatus = $this->getlaststatus();
					switch($laststatus)
					{
						case $this->sysparam['salesstatus']['openorder']	: $this->pageview = 'openorder'; break;
						case $this->sysparam['salesstatus']['ordered']		:
						case $this->sysparam['salesstatus']['bypassed']		:
						case $this->sysparam['salesstatus']['inprogress']	: $this->pageview = 'waiting'; break;
						case $this->sysparam['salesstatus']['edited']		: $this->pageview = 'edited'; break;
						case $this->sysparam['salesstatus']['validated']	: $this->pageview = 'validated'; break;
						default : $this->gotolastpage($laststatus);
					}
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
				$this->timeleft = $rs->value("timeleft");
				
				$this->mbrno = $rs->value('kodemember'); 
				$this->mbrname = $rs->value('namamember'); 
				$this->mbraddress = $rs->value('alamat'); 
				
				$this->paymentcharge = $rs->value('paymentcharge'); 
				if ($this->pageview == 'edited')
				{
					$this->totalorder = $rs->value('totalorderedited'); 
					$this->discount = $rs->value('discountedited'); 
					$this->totalbayar = $rs->value('totalbayaredited'); 
				}
				else
				{
					$this->totalorder = $rs->value('totalorder'); 
					$this->discount = $rs->value('discount'); 
					$this->totalbayar = $rs->value('totalbayar'); 
				}
				
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				
				$this->orderdate 			= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 				= $rs->value('userstatus'); 
				
				$sql = "select * from vw_validateorderd where salesid = " . $this->queryvalue($this->salesid);
				
				$rs1 = $this->db->query($sql);			
				$i = 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] = $rs1->value('itemid');
					$this->items[$i]['itemname'] = $rs1->value('itemname');
					$this->items[$i]['price'] = $rs1->value('price');
					$this->items[$i]['qty'] = $rs1->value('salesqty');
					$this->items[$i]['qtyavail'] = $rs1->value('qtybc') + $rs1->value('purchqty');
					if ($this->pageview == 'edited')
						$this->items[$i]['totalorder'] = ($rs1->value('qtybc') + $rs1->value('purchqty')) * $rs1->value('pricembr');
					else
						$this->items[$i]['totalorder'] = $rs1->value('totalordermember'); 
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

		function sendordertobc()
		{			
			$status = $this->sysparam['salesstatus']['ordered'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotolastpage($status);
		}
		
		function pembayaran()
		{
		}
		
		function confirm()
		{
			$sql = " exec sp_SalesConfirmQtyChange " . $this->queryvalue($this->salesid);			
			$this->db->execute($sql);			
			$this->updatesalesstatus($this->salesid,$this->sysparam['salesstatus']['validated']);
		}
		
		function cancel()
		{
			$status = $this->sysparam['salesstatus']['cancelled'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotolastpage($status);
		}
	}
?>