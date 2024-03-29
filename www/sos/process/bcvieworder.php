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
		var $validatedate;
		var $paiddate;
		var $canceldate;
		var $deliverdate;
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
		var $purchid;
		var $salesidsmi;
		var $cancelcode;
		var $sc;
		var $insufficientitems;
                var $iscleared;
                var $isvalidhours;
		
		function run() 
		{
			parent::run();	
		
			$this->sc = "";
			if ( isset($this->param['sc']) )
				if ( $this->param['sc'] != '' )
				{
					$this->sc = $this->param["sc"];
					$this->sc = str_replace(";","&",str_replace(":", "=", $this->sc)) ;
				}
			
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
					// $this->delivered();
					// SMI want if delivered the data order will disapprear from BC page 
					$this->delivered();
					break;
				case "clear":	
					$this->clearorder();
					break;
				case "ready":	
					$this->setasready();
					break;
				case "none" :	
					$sql = "select st.status from salestable st with (nolock)";
					$sql.= " where st.salesid = ". $this->queryvalue($this->param['salesid']);
                                        $rs = $this->db->query($sql);
                                        if ($rs->fetch())
                                        {
                                            if ($rs->value('status') == $this->sysparam['salesstatus']['ordered'])
                                            {
                                                    $this->createPurchTable();
                                                    $this->checkValidHours();
                                            }
                                        }
                                        else $this->gotohomepage ();
                                        if ($rs) $rs->close();
                                        	
					break;
			}
			$this->loaddata();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        if ($this->status == $this->sysparam['salesstatus']['ordered'])
                        {
                            $this->gapage = "/bc/order/new";
                            $this->gatitle = "Order - BC - View new member order";
                        } else {
                            $this->gapage = "/bc/order/view";
                            $this->gatitle = "Order - BC - View existing member order";
                        }
                        // GOOGLE ANALYTICS PAGE TRACKING
		}
                
                function checkValidHours()
                {
                    $return = false;
                    $this->isvalidhours = false;
                    
                    $sql = "select bcstartwork, bcendwork from bctable with (nolock)
                        inner join salestable with (nolock) on salestable.kodebc = bctable.kodebc
                        where salestable.salesid = " . $this->queryvalue($this->param['salesid']);
                            
                    $rs = $this->db->query($sql);
                    if ($rs && $rs->fetch())
                    {
                        $minutesnow = round(( time() - strtotime("today") ) / 60, 2);
                        $datestart = date ("H:i", strtotime('+'  .$rs->value('bcstartwork') . ' minutes', strtotime("today")));
                        $dateend = date ("H:i", strtotime('+'  .$rs->value('bcendwork') . ' minutes', strtotime("today")));
                        if ($minutesnow >= $rs->value('bcstartwork') && $minutesnow <= $rs->value('bcendwork')) 
                        {
                            $return = true;
                            $this->isvalidhours = true;
                        }
                    }
                    $rs->close();
                    
                    if (!$this->isvalidhours)
                        $this->errmsg .= "Validasi BC hanya dibuka di antara jam " . $datestart . " dan jam " . $dateend . ".<br>";

                    return $return;
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
					
				$this->validatedate			= (is_null($rs->value('validatedate')) ? null : $this->valuedatetime($rs->value('validatedate')));
				$this->paiddate				= (is_null($rs->value('paiddate')) ? null : $this->valuedatetime($rs->value('paiddate')));
				$this->canceldate			= (is_null($rs->value('canceldate')) ? null : $this->valuedatetime($rs->value('canceldate'))); 
				$this->deliverdate			= (is_null($rs->value('deliverdate')) ? null : $this->valuedatetime($rs->value('deliverdate'))); 	
				
                                $this->iscleared                        = (is_null($rs->value('cleardate')) ? false : true); 
                                
				$this->cancelcode			= $rs->value('cancelcode');
				$this->userstatus			= $rs->value('userstatus');
				$this->userstatusinfo                   = $rs->value('statusinfo'); 
				$this->status				= $rs->value('status'); 
				$this->totalorder 			= $rs->value('totalorder'); 
				$this->discount 			= $rs->value('discount'); 
				$this->totalbayar 			= $rs->value('totalorder') + $rs->value('discount'); //$rs->value('totalbayar');
				$this->paymentcharge                    = $rs->value('paymentcharge'); 
				$this->paymentname 			= $rs->value('paymentname'); 
				$this->totalorderbc                     = $rs->value('totalorderbc'); 
				$this->discountbc 			= $rs->value('discountbc'); 
				$this->totalbayarbc                     = $rs->value('totalbayarbc'); 
				
				$sql = "select * from vw_salesline where qty > 0 and salesid = " . $this->queryvalue($this->param['salesid']);
				$rs1 = $this->db->query($sql);			
				$i = 0;
				$this->productrevisi = '';
				if ($this->status >= $this->sysparam['salesstatus']['openorder'] &&
					$this->status <= $this->sysparam['salesstatus']['edited'])
				{
					$this->totalorder		= 0;
					$this->discount 		= 0;
					$this->totalbayar 		= 0;
				}
				$this->insufficientitems = '';
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] 			= $rs1->value('itemid');
					$this->items[$i]['itemname'] 			= $rs1->value('itemname');
					$this->items[$i]['price'] 			= $rs1->value('price');
					$this->items[$i]['salesqty'] 			= $rs1->value('qty');
					$this->items[$i]['totalordermember']            = $rs1->value('totalorder');					
					$this->items[$i]['totalbayarmember']            = $rs1->value('totalbayar');
					$this->items[$i]['qtybc']                       = $rs1->value('qtybc');
					$this->items[$i]['purchqty'] 			= $rs1->value('purchqty');
					$this->items[$i]['shortageqty'] 		= $rs1->value('shortageqty');
					$this->items[$i]['totalorderbc'] 		= $rs1->value('totalorderbc');					
					$this->items[$i]['totalbayarbc'] 		= $rs1->value('totalbayarbc');
					
					if ($this->items[$i]['shortageqty'])
                                        {
						$this->insufficientitems .= ($this->insufficientitems?', ':'') . $this->items[$i]['itemid'] ;
                                        }
                                        
					if ($this->items[$i]['salesqty'] > $this->items[$i]['purchqty'] + $this->items[$i]['qtybc'])
					{
						$this->productrevisi .= ($this->productrevisi != ''? ', ' : '') . $this->items[$i]['itemid'];
					}
					
					if ($this->status >= $this->sysparam['salesstatus']['openorder'] &&
                                                $this->status <= $this->sysparam['salesstatus']['edited'])
                                        {
						$this->items[$i]['totalordermember']	= $rs1->value('totalorderedited');
                                                $this->totalorder               += $rs1->value('totalorderedited'); 
						$this->discount 		+= $rs1->value('discountedited'); 
						$this->totalbayar 		+= $rs1->value('totalbayaredited');					
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
				$this->purchid =  $rs->value('purchid');
				$this->salesidsmi = $rs->value('salesidsmi');
			}
			$rs->close();
		}

		function createPurchTable()
		{			
			$this->updatePurchLine();

			$sql = "if ( not exists(select purchid from PurchTable where PurchId = " . $this->queryvalue($this->param['salesid']) . ") ";
			$sql.= "and exists(select top 1 purchid from Purchline where PurchId = " . $this->queryvalue($this->param['salesid']) . ") ) ";
			$sql.= " insert into PurchTable (purchid, kodebc, orderdate, status) values (" . $this->queryvalue($this->param['salesid']);
			$sql.= "," . $this->queryvalue($this->userid()) . ",getdate(),1)" ;
			$this->db->execute($sql);			
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
				
				if ( $qty >= 0 )
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
                        $this->checkValidHours();
		}
		
		function cancel()
		{
			switch($this->param['backpage'])
			{
				case '1' :
					$searchvalue = $this->param['sc'];
					$searchvalue = str_replace(";","&",str_replace(":", "=", $searchvalue));
					$this->gotopage('report1', ($searchvalue == "" ? "" : $searchvalue . "&pageaction=search") );
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
		
		function nextpage()
		{
			if (!$this->isvalidhours) return;
                        
                        // Check that the order has not been updated since the page was displayed
                        $sql0 = "select top 1 status from salestable where salesid = " . $this->queryvalue($this->param['salesid']);
                        $sql0.= " and status <> ". $this->sysparam['salesstatus']['ordered'];
                        $rs = $this->db->query($sql0);        
                        if ($rs->fetch())
                        {
                            $this->gotopage('vieworder', 'salesid='.urlencode($this->param['salesid']));
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
                                    $sql = "select sum(shortageqty) as shortqty, sum(qtyedited) as totalqty from vw_salesline where salesid = " . $this->queryvalue($this->param['salesid']);
                                    $rs = $this->db->query($sql);

                                    if ($rs->fetch())
                                    {
                                        if ($rs->value('shortqty') > 0) 
                                        {                                
                                            if ($rs->value('totalqty') == 0) 
                                                $this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['cancelled'], $this->sysparam['cancelcode']['emptystock']);
                                            else $this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['edited']);
                                        }
                                        else $this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['validated']);
                                    }
                                    $rs->close();

                                    $this->gotopage('onlineorder');
                            }
                        }
		}
		
		function delivered()
		{
			$this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['delivered']);
                        $this->gotopage('onlineorder');
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