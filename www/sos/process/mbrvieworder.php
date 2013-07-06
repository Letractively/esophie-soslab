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
		var $statuscode;
		var $orderdate;
		var $createddate;
		var $items;
		var $pageview;
		var $timeleft;
		var $isanyitemsold;
		var $validatesameday;
		var $mbrmsg;
		var $errorbcmsg;
		var $lastorderstatus;
		var $defaultbckode;
		var $paymdate;
		var $paymref;
		var $defaultbc;
		var $choosebc;
		
		function run() 
		{	
			parent::run();				
			
			$this->salesid = $this->param['salesid'];
			if ($this->salesid == '') $this->gotohomepage();
			
			if ( isset($this->param['bc']) )
				$this->choosebc = $this->param['bc'];
			
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
					$this->orderbaru();
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
				
				case "orderhistory":
					$this->gotopage('orderhistory');
					break;
                                
                                case "success":
					$this->callback_success();
					break;
                                    
				case "failure":
					$this->callback_failure();
					break;

				// default view	
				case "none":	
					$this->isanyitemsold = $this->checkItemSold();
                                        //$this->checksalesopenorder();
                                        break;
			}
			$this->loaddata();

			if ( $this->action == "refreshbc" )
				$this->refreshbc();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{					
				if ($rs->value("status") == $this->sysparam['salesstatus']['clear']) $this->gotohomepage();
                                $this->setpageview($rs->value("status"));
				$this->setmbrmsg();
				
				//  All information
				if ($rs->value("status") < $this->sysparam['salesstatus']['validated']) 
					$this->timeleft 		= $rs->value("timeleft");
				else 
					$this->timeleft 		= $rs->value("timeleftpaid");
				
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
				$this->statuscode 	= $rs->value('status'); 
				
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
				
				// Check BC
				$sql = "select count(*) as hasbc from mappingtable where kodebc != '' and kodemember = '" . $this->mbrno . "'";
				$rs2 = $this->db->query($sql);			
				if ($rs2->fetch()) 
				{
					if ( $rs2->value('hasbc') <= 0 )
						$this->errorbcmsg = "Silahkan hubungi Customer Care untuk daftar ke salah satu BC dahulu.";
					else 
						$this->errorbcmsg = "";
				}
			}
			else
			{
				$rs->close();
				$this->gotohomepage();				
			}
			$rs->close();
			
			
			$sql = "select top 1 salesid, status from vw_salestable where kodemember = " . $this->queryvalue($this->userid());
			$sql.= " and status <> " . $this->queryvalue($this->sysparam['salesstatus']['clear']);
			$sql.= " order by salesid desc";
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->lastorderstatus = $rs->value('status');
			}
			$rs->close();
                        
                        if ($this->debug()) echo "<br/>Payment Mode: " . $this->varvalue('paymentmode');
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
			
                        if ($this->debug())  echo "<br/>Pageview: " . $this->pageview;
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
				$result = str_replace( $number, "", $this->param['handphone'] );
				if ( strlen(trim($result)) > 0 )
				{
					$this->errmsg = "Nomor handphone harus diisi dengan angka saja";
					return;
				} else {
                                    $mobile = trim($this->param['handphone']);   
                                }
			}
			else
			{
				$this->errmsg = 'handphone harus diisi';
				return;
			}
			
			$sql = 'update salestable set ';
			$sql.= 'telp = ' . $this->queryvalue($mobile);
			$sql.= ',email = ' . $this->queryvalue(trim($this->param['email']));
			$sql.= 'from salestable where salesid=' . $this->queryvalue($this->salesid);			
			$this->db->execute($sql);

			$sql = 'update membertable set ';
			$sql.= 'phone = ' . $this->queryvalue($mobile);
			$sql.= ',email = ' . $this->queryvalue(trim($this->param['email']));
			$sql.= 'where kodemember=(select top 1 kodemember from salesTable where salesid=' . $this->queryvalue($this->salesid). ')';
			$this->db->execute($sql);

			$status = $this->sysparam['salesstatus']['ordered'];
			$this->updatesalesstatus($this->salesid,$status);
			$this->gotopage('orderhistory','salesid='.urlencode($this->salesid));
		}
		
		function batalorder()
		{
			$this->updatesalesstatus($this->salesid, $this->sysparam['salesstatus']['cancelled'], $this->sysparam['cancelcode']['bymember']); // cancelled
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
                        $this->initfaspay($this->salesid);
		
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
			//if (!$this->isvaliddata() ) return;
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
                
                function orderbaru ()
		{
			$sql = "delete from salesline where salesid=".$this->queryvalue($this->salesid);
			$this->db->query($sql);

			$sql = "delete from salesTable where salesid=".$this->queryvalue($this->salesid);
			$this->db->query($sql);
			
			$this->gotopage("inputitem");
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
							/*
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
			
			// Checking min order and max order
			$sql = "select top 1 isnull(totalorder,0) as totalorder from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
			    $this->totalorder = $rs->value('totalorder'); 
			}
			$rs->close ();
			
			$sql = "select top 1 mintotalsales, maxtotalsales from sysparamTable";
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
			    $mintotalsales = $rs->value('mintotalsales'); 
			    $maxtotalsales = $rs->value('maxtotalsales'); 
			}
			$rs->close ();
			
			//echo $this->totalorder . '-' .$maxtotalsales . '-' . $mintotalsales;
			if ( $this->totalorder > $maxtotalsales || $this->totalorder < $mintotalsales )
			{
			    $this->errmsg = 'Minimum order harus diatas IDR ' . $this->valuenumber($mintotalsales) . ' dan maximum order IDR ' . $this->valuenumber($maxtotalsales);
                            $ret = false;    
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
                $this->defaultbckode = $this->param["bc"];
			}
			
			$sql = "select* from vw_BCMapping ";
			$sql.= "where KodeMember = " . $this->queryvalue($this->userid());
			
			$this->setselectoption('bc', $sql, 'kodebc', 'label', $this->choosebc);
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
                
                                
		function callback_success()
		{
			// do nothing 
		}
		
		function callback_failure()
		{
			$this->pageview = 'paymfailure';
		}
	
		function refreshbc ( ) 
		{
			if ( isset($this->choosebc) )
			{
				$sql = "select isnull(defaultbc,0) as defaultbc from mappingTable where kodebc ='".$this->choosebc."' and kodemember = '".$this->mbrno."'";
				if ( $this->db->executeScalar($sql) )
					$this->defaultbc = '1';
				else
					$this->defaultbc = '0';
				
				
			}
		}
		
	}
?>