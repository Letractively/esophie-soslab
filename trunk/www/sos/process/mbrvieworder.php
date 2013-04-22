<?
	class mbrvieworder extends controller
	{	
		var $salesid;
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $bcphone;
		var $mbrno;
		var $mbrname;
		var $mbraddress;
		var $mbrphone;
		var $mbremail;
		var $totalorder;
		var $discount;
		var $paymentcharge;
		var $paymentname;
		var $paymentmode;
		var $totalbayar;
		var $status;
		var $orderdate;
		var $createddate;
		var $items;
		var $pageview;
		var $timeleft;
		var $isanyitemsold;
		var $validatesameday;
		var $mbrmsg;
		
		function run() 
		{	
			parent::run();				
			
			$this->salesid = $this->param['salesid'];
			if ($this->salesid == '') $this->gotohomepage();
			
			switch($this->action)
			{	
				//orderedit
				case "confirmorder":
					$this->confirmorder();				
					break;
				case "refresh":
					$this->refresh();
					break;
				case "tambah":
					$this->savebc();
					$this->gotopage('inputitem','salesid='.urlencode($this->salesid));
					break;
				case "neworder":
					$this->gotopage('neworder');
					break;
				
				//orderconfirm
				case "sendordertobc":
					$this->sendordertobc();
					break;
				case "batalorder":
					$this->batalorder();				
					break;
				case "back":
					$this->gotopage('paymentmethod','salesid='.urlencode($this->salesid));
					break;
				
				//confirmqtychange
				case "confirmqtychange":
					$this->confirmqtychange();
					break;
				case "cancel":
					$this->cancel();
					break;
					
				//pembayaran				
				case "pembayaran":
					$this->pembayaran();
					break;
					
				case "none":	
					$sql = "select status from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
					$status = $this->db->executescalar($sql);
					$this->isanyitemsold = $this->checkItemSold();
					switch($status)
					{
						case $this->sysparam['salesstatus']['clear'] : $this->gotohomepage(); break;
						default :$this->setpageview($status); break;
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
				$this->setmbrmsg();
				
				$this->timeleft 		= $rs->value("timeleft");
				
				$this->mbrno 			= $rs->value('kodemember'); 
				$this->mbrname 			= $rs->value('namamember'); 
				$this->mbraddress 		= $rs->value('alamat'); 
				$this->mbrphone 		= $rs->value('telp'); 
				$this->mbremail 		= $rs->value('email'); 
				
				$this->paymentcharge 	= $rs->value('paymentcharge'); 
				$this->paymentname 		= $rs->value('paymentname'); 
				$this->paymentmode 		= $rs->value('paymentmode'); 
				
				if ($this->pageview == 'confirmqtychange')
				{
					$this->totalorder 	= 0;
					$this->discount 	= 0;
					$this->totalbayar 	= $this->paymentcharge; 
				}
				else
				{
					$this->totalorder 	= $rs->value('totalorder'); 
					$this->discount 	= $rs->value('discount'); 
					$this->totalbayar 	= $rs->value('totalbayar'); 
				}
				
				$this->bcno 			= $rs->value('kodebc'); 
				$this->bcname 			= $rs->value('namabc'); 
				$this->bcaddress 		= $rs->value('alamatbc');
				$this->bcphone 			= $rs->value('telpbc');
				$this->validatesameday 	= $rs->value('validatesameday'); 
				$this->param["bc"]		= $this->bcno;
				
				$this->orderdate 	= $this->valuedatetime($rs->value('orderdate')); 
				$this->createddate 	= $this->valuedatetime($rs->value('createddate')); 
				
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
				$this->gotohomepage();				
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
				default: $this->pageview = 'view';
			}
			
		}
		
		function checkItemSold()
		{
			$sql = 'select count(*) from vw_salesLine where isnull(qtyEdited,0) > 0 and salesid=' . $this->queryvalue($this->salesid);			
			$anyItem = $this->db->executeScalar($sql);
			return $anyItem;
		}
		
		//orderconfirm-----------------------------------------------------------------------------
		function sendordertobc()
		{			
			if (trim($this->param['handphone']) != '')
			{
				$number = array("0","1","2","3","4","5","6","7","8","9");
				$mobile = trim($this->param['handphone']);
				$result = str_replace( $number, "", $mobile );
				if ( strlen($result) > 0 )
				{
					$this->errmsg = "Nomor handphone harus diisi dengan angka saja";
					return;
				}
			}
			else
			{
				$this->errmsg = 'handphone harus diisi';
				return;
			}
			
			$sql = 'update salestable set ';
			$sql.= 'telp = ' . $this->queryvalue($this->param['handphone']);
			$sql.= ',email = ' . $this->queryvalue($this->param['email']);
			$sql.= 'from salestable where salesid=' . $this->queryvalue($this->salesid);			
			$this->db->execute($sql);

			$sql = 'update membertable set ';
			$sql.= 'phone = ' . $this->queryvalue($this->param['handphone']);
			$sql.= ',email = ' . $this->queryvalue($this->param['email']);
			$sql.= 'where kodemember=(select top 1 kodemember from salesTable where salesid=' . $this->queryvalue($this->salesid). ')';
			$this->db->execute($sql);

			$status = $this->sysparam['salesstatus']['ordered'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotopage('orderhistory','salesid='.urlencode($this->salesid));
		}
		
		function batalorder()
		{
			$this->updatesalesstatus($this->salesid, 0, $this->sysparam['cancelcode']['bymember']); // cancelled
			$this->gotohomepage();
		}
		
		//Pembayaran ---------------------------------------------------------------
		function pembayaran()
		{
			$this->gotopage('paymentconfirm','salesid='.urlencode($this->salesid));
		}
		
		//confirmqtychange-------------------------------------------------------
		function confirmqtychange()
		{
			$sql = " exec sp_SalesConfirmQtyChange " . $this->queryvalue($this->salesid);			
			$this->db->execute($sql);			
			$this->updatesalesstatus($this->salesid,$this->sysparam['salesstatus']['validated']);
		
			$this->gotopage('paymentconfirm','salesid='.urlencode($this->salesid));
		}

		function cancel()
		{
			$status = $this->sysparam['salesstatus']['cancelled'];
			$this->updatesalesstatus($this->salesid, $status, $this->sysparam['cancelcode']['revisi']);
			$this->gotohomepage();
		}
		
		//orderedit ----------------------------------------------------------------------------
		function refresh()
		{
			if (!$this->isvaliddata() ) return;
			$this->savebc();
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
				$this->gotopage('paymentmethod','salesid='.urlencode($this->salesid));
		}
		
		function savebc() 
		{			
			if (isset($this->param["defaultbc"]))
			{
				$sql = "update mappingTable set ";
				$sql.= " defaultbc = 0 where kodemember = " . $this->queryvalue($this->userid());
				$this->db->execute($sql);
				
				$sql = "update mappingTable set ";
				$sql.= " defaultbc = 1 where kodemember = " . $this->queryvalue($this->userid());
				$sql.= " and kodebc = " . $this->queryvalue($this->param["bc"]);
				$this->db->execute($sql);
			}
						
			$sql = "update salesTable set ";			
			$sql.= " kodebc = " . $this->queryvalue($this->param["bc"]);
			$sql.= " where salesid = " . $this->queryvalue($this->salesid);			

			$this->db->execute($sql);	
		}
		
		function isvaliddata()
		{
			$ret = true;
			if ( isset($this->param['itemid']) == false )
			{
				$this->errmsg = "Pemesanan barang harus ada agar dapat lanjut ke tahap berikut";
				return false;
			}
			
			$this->errmsg = '';
			for ($i=0;$i<count($this->param['itemid']);$i++)
			{
				$itemid = $this->param['itemid'][$i];
				
				if ($itemid != '') 
				{					
					if ($this->param['itemqty'][$i] == '')		
					{
						$this->errmsg[$itemid] = 'Item ' . $itemid . ' quantity harus diisi';
					}
					else														
						if (!is_numeric($this->param['itemqty'][$i]))
						{
							$this->errmsg[$itemid] = 'Item ' . $itemid . ' quantity harus numeric';						
						}
						else
						{
							if (floatval($this->param['itemqty'][$i]) <= 0)
							{
								$this->errmsg[$itemid] = 'Item ' . $itemid . ' quantity harus lebih besar dari 0';									
							}
							else
							{
								$sql = "exec sp_checkQuantity" . $this->queryvalue($itemid);
								$qtyStock = $this->db->executeScalar($sql);
								$qtyOrder = $this->param['itemqty'][$i];
								if ($qtyStock - $qtyOrder < 0 )
								{
									$this->errmsg[$itemid] = 'Item ' . $itemid . ' stock item tidak mencukupi';
								}						
							}
						}
				}
			}
			
			return $ret;			
		}
		
		function getbc()
		{
			if (!isset($this->param["bc"]) || $this->param["bc"] == '') 
			{	
				$sql = "select kodebc from vw_BCMapping where KodeMember = " . $this->queryvalue($this->userid());
				$sql.= " and defaultbc = 1";
				$this->param["bc"] = $this->db->executeScalar($sql);
			}
			
			$sql = "select* from vw_BCMapping ";
			$sql.= "where KodeMember = " . $this->queryvalue($this->userid());
			$this->setselectoption('bc', $sql, 'kodebc', 'label', $this->param["bc"]);
		}
		
		function setselectedoption($name,$rs) 
		{	
			switch($name) 
			{
				case "bc":
					if (!isset($this->param["bc"]) || $this->param["bc"] == '')
						$this->param["bc"] = $rs->value('kodebc');
						
					$this->bcno = $rs->value('kodebc');
					$this->bcname = $rs->value('namabc');
					$this->bcaddress = $rs->value('alamat');
					$this->bcphone = $rs->value('telp');
					break;
			}
			
		}
		
		

	}
?>