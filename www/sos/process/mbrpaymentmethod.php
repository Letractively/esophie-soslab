<?
	class mbrpaymentmethod extends controller
	{	
		var $salesid;
		var $items;
		var $pageview;
		var $selectedpaymentmode;
		
		function run() 
		{	
			parent::run();	
			
			$this->checksalesid();
                        
                        // If salesid is provided but the status is not open order => forward to homepage
                        if (strlen($this->salesid) > 0)
                        {
                            $sql = 'select count(*) from salestable ';
                            $sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
                            $sql.= ' and status = ' . $this->sysparam['salesstatus']['openorder'];
                            $sql.= ' and salesid = ' . $this->queryvalue($this->salesid);

                            if(!$this->db->executeScalar($sql)) $this->gotohomepage();
                        }
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/member/order/payment/select";
                        $this->gatitle = "Order - Member - Select order mode of payment";
                        // GOOGLE ANALYTICS PAGE TRACKING

			switch($this->action)
			{
				case "back":
					header('location:mbrvieworder.php?edit=1&salesid=' . $this->salesid);
					exit;
					break;
				case "confirm":
					$this->confirm();
					break;
				case "none":					
					$this->checksalesopenorder();					
					break;
			}
			$this->load();
		}		
		
		function load()
		{
			$sql = 'select totalbayar, paymentmode from salestable where salesid = ' .  $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);
			if ($rs->fetch())
			{
				$this->selectedpaymentmode = $rs->value('paymentmode');
				$totalbayar = $rs->value('totalbayar');
			}
			$rs->close();
			
			$sql = "select paymentmode, name, description, isnull(inputMobileNumber,0) as inputmobilenumber, chargeratio, chargefee, chargethreshold  from paymentMode with (NOLOCK) where active=1 order by seqno";
			$rs = $this->db->query($sql);			
			$i = 0;
			while ($rs->fetch()) 
			{
				$this->items[$i]["paymentmode"] = $rs->value('paymentmode');
				$this->items[$i]["name"] = $rs->value('name');
                                if ($rs->value('chargethreshold') >= 0 && $rs->value('chargethreshold') <= $totalbayar)
                                {
                                        $this->items[$i]["fee"] = 'Biaya administrasi gratis jika total pembarayan > Rp ' .$this->valuenumber($rs->value('chargethreshold'));
                                        $this->items[$i]["totalfee"] = 0;
                                }
				else if ($rs->value('chargeratio') > 0 && $rs->value('chargefee') > 0)
				{
					$chargefee = ($totalbayar * $rs->value('chargeratio')) / (100 + $rs->value('chargeratio'));
					$this->items[$i]["fee"] = 'Charge fee: ' .$rs->value('chargeratio'). '% x Rp ' . $this->valuenumber($totalbayar) . ' = Rp. ' . $this->valuenumber($chargefee) . '';
					$this->items[$i]["fee"].= '<br>Fixed fee: Rp. ' . $this->valuenumber($rs->value('chargefee')) . ' per transaksi';
					$this->items[$i]["fee"].= '<br>Total fee: Rp. ' . $this->valuenumber($chargefee+$rs->value('chargefee')) . '';
					$this->items[$i]["totalfee"] = $chargefee+$rs->value('chargefee');
				}
				else
				{
					if ($rs->value('chargeratio') > 0)
					{
						$chargefee = ($totalbayar * $rs->value('chargeratio')) / (100 + $rs->value('chargeratio'));
						$this->items[$i]["fee"] = 'Charge fee: ' .$rs->value('chargeratio'). '% x Rp ' . $this->valuenumber($totalbayar) . ' = Rp. ' . $this->valuenumber($chargefee) . '';
						$this->items[$i]["fee"].= '<br>Total fee: Rp. ' . $this->valuenumber($chargefee) . '';
						$this->items[$i]["totalfee"] = $chargefee;
					}
					if ($rs->value('chargefee') > 0)
					{
						$this->items[$i]["fee"] = 'Fixed fee: Rp. ' . $this->valuenumber($rs->value('chargefee')) . ' per transaksi';
						$this->items[$i]["fee"].= '<br>Total fee: Rp. ' . $this->valuenumber($rs->value('chargefee')) . '';
						$this->items[$i]["totalfee"] = $rs->value('chargefee');
					}
				}	
				$this->items[$i]["description"] = $rs->value('description');
				$this->items[$i]["mobilenumber"] = $rs->value('inputmobilenumber');
				$i++;
			}
			$rs->close();

		}
	
		function confirm()
		{	
			if (isset($this->param["mop"]))
			{				
				$mobnumber = isset($this->param["mobilenumber"]) ? trim($this->param["mobilenumber"]) : '';
                                $sql = " exec sp_updatePaymentMode " . $this->queryvalue($this->salesid) . "," . $this->queryvalue($this->param["mop"]) . "," . $this->queryvalue($mobnumber);
				$this->db->execute($sql);	
				header('location:mbrvieworder.php?salesid=' . $this->salesid);
			}
			else
			{
				$this->errmsg = 'Silahkan pilih salah satu cara pembayaran';
			}
		}
	}
?>