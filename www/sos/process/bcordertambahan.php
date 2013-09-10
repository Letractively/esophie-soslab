<?
	class bcordertambahan extends controller
	{	
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $totalorder;
		var $discount;
		var $totalbayar;
		var $includeppn;
		var $items;
		var $status;
		
		function run() 
		{	
			parent::run();	
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/order/orderbc";
                        $this->gatitle = "Order - BC - Order BC ";
                        // GOOGLE ANALYTICS PAGE TRACKING
			
			//if (!isset($this->param['salesid']) || $this->param['salesid'] == '')
			//	$this->gotopage('onlineorder');
				
			switch($this->action)
			{
				case 'kembali':
					$this->kembali();
					break;
				case 'setuju':
					$this->nextpage();
					break;
				case 'bcorder':
					$this->bcorder();
					break;
			}
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = "select * from vw_purchtable where purchid = " . $this->queryvalue($this->param['salesid']);
			$sql.= " and kodebc = " .  $this->queryvalue($this->userid());
			
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->bcno = $rs->value('kodebc'); 
				$this->bcname = $rs->value('namabc'); 
				$this->bcaddress = $rs->value('alamatbc'); 
				
				$this->totalorder = $rs->value('totalorder'); 
				$this->discount = $rs->value('discount'); 				
				$this->totalbayar = $rs->value('totalbayar'); 
				$this->includeppn = $rs->value('includeppn');
				$this->status = $rs->value('status');
				$this->orderdate = $rs->value('orderdate');
				
				$sql = "select * from vw_purchline where purchid = " . $this->queryvalue($this->param['salesid']);
				$sql.= " and qty > 0";
				$rs1 = $this->db->query($sql);			
				$i = 0;
				while ($rs1->fetch()) 
				{
					$this->items[$i]['itemid'] = $rs1->value('itemid');
					$this->items[$i]['itemname'] = $rs1->value('itemname');
					$this->items[$i]['price'] = $rs1->value('price');
					$this->items[$i]['pricebc'] = $rs1->value('pricebc');
					$this->items[$i]['qty'] = $rs1->value('qty');
					$this->items[$i]['totalorder'] = $rs1->value('totalorder');
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

		function nextpage()
		{                        
                        $sql0 = "select top 1 status from salestable where salesid = " . $this->queryvalue($this->param['salesid']);
                        $sql0.= " and status <> ". $this->sysparam['salesstatus']['ordered'];
                        $rs = $this->db->query($sql0);        
                        if ($rs->fetch())
                        {
                            $this->gotopage('vieworder', 'salesid='.urlencode($this->param['salesid']));
                        }
                        else 
                        {
                            $this->updatesalesstatus($this->param['salesid'],$this->sysparam['salesstatus']['inprogress']);
                            $this->gotopage('onlineorder');
                        }
		}
		
		function bcorder()
		{
			$this->gotopage('vieworder', 'salesid='.urlencode($this->param['salesid']));
		}
		
		function kembali()
		{
			$this->gotopage('vieworder','salesid='.urlencode($this->param['salesid']));
		}
	}
?>