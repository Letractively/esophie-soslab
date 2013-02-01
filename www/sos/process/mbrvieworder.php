<?
	class mbrvieworder extends controller
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
		var $paymentcharge;
		var $paymentname;
		var $paymentmode;
		var $totalbayar;
		var $status;
		var $orderdate;
		var $items;
		var $pageview;
		var $timeleft;
		var $isanyitemsold;
		var $validatesameday;
		
		function run() 
		{	
			parent::run();				
			$this->salesid = $this->salesid();
			switch($this->action)
			{	
				case "confirmorder":
					$this->confirmorder();				
					break;
				case "refresh":
					$this->refresh();
					break;
				case "tambah":
					$this->gotopage('inputitem');
					break;
				case "sendordertobc":
					$this->sendordertobc();
					break;
				case "confirmqtychange":
					$this->confirmqtychange();
					break;
				case "cancel":
					$this->cancel();
					break;
				case "pembayaran":
					$this->pembayaran();
					break;
				case "none":					
					$laststatus = $this->getlaststatus();
					$this->isanyitemsold = $this->checkItemSold();
					switch($laststatus)
					{
						case $this->sysparam['salesstatus']['openorder']	: 
						case $this->sysparam['salesstatus']['ordered']		:
						case $this->sysparam['salesstatus']['bypassed']		:
						case $this->sysparam['salesstatus']['inprogress']	: 
						case $this->sysparam['salesstatus']['edited']		: 
						case $this->sysparam['salesstatus']['validated']	: $this->setpageview($laststatus); break;
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
				$this->setpageview($rs->value("status"));
				
				$this->timeleft = $rs->value("timeleft");
				
				$this->mbrno 		= $rs->value('kodemember'); 
				$this->mbrname 		= $rs->value('namamember'); 
				$this->mbraddress 	= $rs->value('alamat'); 
				
				$this->paymentcharge 	= $rs->value('paymentcharge'); 
				$this->paymentname 	= $rs->value('paymentname'); 
				$this->paymentmode 	= $rs->value('paymentmode'); 
				
				if ($this->pageview == 'confirmqtychange')
				{
					$this->totalorder = 0;
					$this->discount = 0;
					$this->totalbayar = $this->paymentcharge; 
				}
				else
				{
					$this->totalorder = $rs->value('totalorder'); 
					$this->discount = $rs->value('discount'); 
					$this->totalbayar = $rs->value('totalbayar'); 
				}
				
				$this->bcno 		= $rs->value('kodebc'); 
				$this->bcname 		= $rs->value('namabc'); 
				$this->bcaddress 	= $rs->value('alamatbc');
				$this->validatesameday 	= $rs->value('validatesameday'); 
				
				$this->orderdate 	= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 		= $rs->value('userstatus'); 
				
				$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->salesid);
				
				$rs1 = $this->db->query($sql);			
				$i = 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] = $rs1->value('itemid');
					$this->items[$i]['itemname'] = $rs1->value('itemname');
					$this->items[$i]['price'] = $rs1->value('price');
					$this->items[$i]['qty'] = $rs1->value('qty');
					$this->items[$i]['qtyavail'] = $rs1->value('qtybc') + $rs1->value('purchqty');
					if ($this->pageview == 'confirmqtychange')
					{
						$this->items[$i]['totalorder'] = $rs1->value('totalorderedited');
						$this->totalorder 	+= $rs1->value('totalorderedited');
						$this->discount 	+= $rs1->value('discountedited');
						$this->totalbayar 	+= $rs1->value('totalbayaredited'); 
					}
					else
						$this->items[$i]['totalorder'] = $rs1->value('totalorder'); 
					$i++;
				}
				$rs1->close();			
			}
			else
			{
				$rs->close();
				$this->gotopage('memberinfo');
			}
			$rs->close();
		}

		function setpageview($status)
		{
			switch($status)
			{
				case $this->sysparam['salesstatus']['openorder']	: 
						if (isset($this->param['edit']) && $this->param['edit'] == "1")
							$this->pageview = 'orderedit'; 		
						else
							$this->pageview = 'orderconfirm'; 
					break;
				case $this->sysparam['salesstatus']['ordered']		:
				case $this->sysparam['salesstatus']['bypassed']		:
				case $this->sysparam['salesstatus']['inprogress']	: $this->pageview = 'waiting'; break;
				case $this->sysparam['salesstatus']['edited']		: $this->pageview = 'confirmqtychange'; break;
				case $this->sysparam['salesstatus']['validated']	: $this->pageview = 'pembayaran'; break;
			}
		}
		
		function refresh()
		{
			if (!isset($this->param['itemid'])) return;
			for($i=0;$i<count($this->param['itemid']);$i++)
			{
				if (is_numeric($this->param["itemqty"][$i]))
				{
					$sql = "exec sp_updateSalesLine " . $this->queryvalue($this->salesid) . "," . $this->queryvalue($this->param['itemid'][$i]) . "," . $this->param["itemqty"][$i] . ",0";				
					$this->db->execute($sql);				
				}
			}
			
			$sql = "exec sp_updateSalesTotal " . $this->queryvalue($this->salesid);					
			$this->db->execute($sql);	
		}
		
		function confirmorder()
		{
			$this->refresh();
			if ( $this->isvaliddata() )
			{
				$this->gotopage('paymentmethod');
			}
		}
		
		function sendordertobc()
		{			
			$status = $this->sysparam['salesstatus']['ordered'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotolastpage($status);
		}
		
		function pembayaran()
		{
			$this->gotopage('paymentconfirm');
		}
		
		function confirmqtychange()
		{
			$sql = " exec sp_SalesConfirmQtyChange " . $this->queryvalue($this->salesid);			
			$this->db->execute($sql);			
			$this->updatesalesstatus($this->salesid,$this->sysparam['salesstatus']['validated']);
			$this->gotolastpage($status);
			
			$this->gotopage('paymentconfirm');
		}

		function checkItemSold()
		{
			$sql = "select count(*) from vw_salesLine where isnull(qtyEdited,0) > 0 and salesid=" . $this->queryvalue($this->salesid);			
			$anyItem = $this->db->executeScalar($sql);
			return $anyItem;
		}

		function cancel()
		{
			$status = $this->sysparam['salesstatus']['cancelled'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotolastpage($status);
		}
		
		function isvaliddata()
		{
			$ret = true;
			if ( isset($this->param['itemid']) == false )
			{
				$this->param['mbrvieworder_error'] = "Pemesanan barang harus ada agar dapat lanjut ke tahap berikut";
				return false;
			}
			
			for ($i=0;$i<count($this->param['itemid']);$i++)
			{
				$errname = "item".$i."err";
				$this->param[$errname] = '';

				if ($this->param['itemid'][$i] != '') 
				{					
					if ($this->param['itemqty'][$i] == '')		
					{
						if ($this->param[$errname] == '')
							$this->param[$errname] = "quantity harus di isi";										
					}
					else														
						if (!is_numeric($this->param['itemqty'][$i]))
						{
							$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
							$this->param[$errname] .= "quantity harus numeric";										
						}
						else
						{
							if (floatval($this->param['itemqty'][$i]) <= 0)
							{
								$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
								$this->param[$errname] .= "quantity harus lebih besar dari 0";										
							}
							/* Check stock desactivated for members
							else
							{
								$sql = "exec sp_checkQuantity" . $this->queryvalue($this->param['itemid'][$i]);
								$qtyStock = $this->db->executeScalar($sql);
								$qtyOrder = $this->param['itemqty'][$i];
								if ($qtyStock - $qtyOrder < 0 )
								{
									$this->param[$errname] = 'stock item tidak mencukupi';
								}						
							}
							*/
						}
				}
				else
				{			
					if (!$this->param['itemqty'][$i] == '')	
					{
						$this->param[$errname] = "kode item tidak boleh kosong";
						
						if (!is_numeric($this->param['itemqty'][$i]))
						{
							$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
							$this->param[$errname] .= "quantity harus numeric";
						}
						else
						{
							if (floatval($this->param['itemqty'][$i]) <= 0)
							{
								$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
								$this->param[$errname] .= "quantity harus lebih besar dari 0";										
							}
						}
					}
				}
				
				if ($this->param[$errname] != '' )
				{
					$this->param[$errname] = ucfirst($this->param[$errname]) . ".";
					$ret = false;
				}
			}
			
			return $ret;			
		}

	}
?>