<?
	class mbrorder extends controller
	{	
		var $salesid;
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $bcphone;
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
		var $errorbcmsg;
		var $defaultbckode;
		
		function run() 
		{	
			parent::run();				
			$this->checksalesid();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{									
				$this->timeleft 		= $rs->value("timeleft");
				
				$this->mbrno 			= $rs->value('kodemember'); 
				$this->mbrname 			= $rs->value('namamember'); 
				$this->mbraddress 		= $rs->value('alamat'); 
				
				$this->paymentcharge 	= $rs->value('paymentcharge'); 
				$this->paymentname 		= $rs->value('paymentname'); 
				$this->paymentmode 		= $rs->value('paymentmode'); 
				
				$this->totalorder 		= $rs->value('totalorder'); 
				$this->discount 		= $rs->value('discount'); 
				$this->totalbayar 		= $rs->value('totalbayar'); 
					
				$this->bcno 			= $rs->value('kodebc'); 
				$this->bcname 			= $rs->value('namabc'); 
				$this->bcaddress 		= $rs->value('alamatbc');
				$this->bcphone 			= $rs->value('telpbc');
				$this->validatesameday 	= $rs->value('validatesameday'); 
				
				
				$this->orderdate 		= $this->valuedatetime($rs->value('orderdate')); 
				$this->status 			= $rs->value('userstatus'); 
				
				$this->loaddata_update($rs);
				$this->loadsalesline();		
			}
			else
			{
				$rs->close();
				$this->gotohomepage();				
			}
			$rs->close();
			
			$sql = "select count(*) from vw_BCMapping ";
			$sql.= "where KodeMember = " . $this->queryvalue($this->userid());
			if ( $this->db->executeScalar($sql) <= 0 )
				$this->errorbcmsg = "Silahkan hubungi Customer Care di " . $this->sysparam['app']['custservicenumber'] . " untuk pilih BC dahulu.";
		}
		
		function loaddata_update($rs) {}
		
		function loadsalesline()
		{
			$sql = "select * from vw_salesline where salesid = " . $this->queryvalue($this->salesid);
				
			$rs = $this->db->query($sql);			
			$i = 0;
			while ($rs->fetch()) 
			{
				$this->items[$i]['itemid'] = $rs->value('itemid');
				$this->items[$i]['itemname'] = $rs->value('itemname');
				$this->items[$i]['price'] = $rs->value('price');
				$this->items[$i]['qty'] = $rs->value('qty');
				$this->items[$i]['qtyavail'] = $rs->value('qtybc') + $rs->value('purchqty');
				$this->items[$i]['totalorder'] = $rs->value('totalorder'); 
				$this->loadsalesline_update($rs);
				$i++;
			}
			$rs->close();	
		}
		
		function loadsalesline_update($rs) {}
				
		function printitems($edit=false)
		{
			$ret = '';
			if (is_array($this->items))
			{
				$i=0;
				foreach ($this->items as $item)
				{					
					$ret.= $i++%2?'<tr class="pinkrow">':'<tr>';
					$ret.= '<td align="left">' . $item['itemid'] . '</td>';
					$ret.= '<td align="left">' . htmlspecialchars($item['itemname']). '</td>';
					$ret.= '<td align="right">' . $this->valuenumber($item['price']) . '</td>';
					if ($edit)
					{	
						$ret.= '<td>';
						$ret.= '<input type="hidden" name="itemid[]" id="itemid[]" value="' . $item['itemid'] . '">';
						$ret.= '<input type="textbox" name="itemqty[]" id="itemqty[]" value="' . $item['qty'] . '" maxlength="3" size="3" style="text-align:right">';
						$ret.= '</td>';
					}
					else
					{
						$ret.=  '<td align="right">' . $this->valuenumber($item['qty']) . '</td>';
					}	

					$ret.=  '<td align="right">' . $this->valuenumber($item['totalorder']) . '</td>';
					$ret.=  '</tr>';
					
					if ($this->value('item'.($i-1).'err') != '') { 
						$ret.=  '<tr><td colspan="5"><div class="boxerr1">' . $this->value('item'.($i-1).'err') . '</div></td></tr>';
					}
				}
				if ($edit)
				{
					$ret.=  $i++%2?'<tr class="pinkrow"':'<tr';
					$ret.=  ' style="cursor:pointer" onclick="setaction(\'tambah\');">';
					$ret.=  '<td colspan="5" align="center" style="text-decoration: underline;">Tambah Product</td>';
					$ret.=  '</tr>';					
				}
			}
			else
			{
				$ret.=  '<td colspan="5" align="center">no items</td>';
			}	
			return $ret;			
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
	}
?>