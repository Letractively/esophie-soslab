<?
	class bconlineorder extends controller
	{	
		var $neworders;
		var $orderstodeliver;
		var $orderstofollowup;
		var $statuscount;
                var $showneworders;
		
		function run() 
		{	
			parent::run();	
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/homepage";
                        $this->gatitle = "Order - BC - Homepage";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$sql = 'select * from vw_salestatusperbc where kodebc = ' . $this->queryvalue($this->userid());  
			$rs = $this->db->query($sql);
			$this->statuscount['batal'] 		= 0;
			$this->statuscount['orderbaru'] 	= 0;
			$this->statuscount['dalamproses'] 	= 0;
			$this->statuscount['revisi'] 		= 0;
			$this->statuscount['belumbayar'] 	= 0;
                        $this->statuscount['confirmed'] 	= 0;
			$this->statuscount['telahbayar'] 	= 0;
			$this->statuscount['siap'] 		= 0;
			$this->statuscount['delivered'] 	= 0;
			if ($rs->fetch())
			{
				$this->statuscount['batal'] 		= $rs->value('batal');
				$this->statuscount['orderbaru'] 	= $rs->value('orderbaru');
				$this->statuscount['dalamproses'] 	= $rs->value('dalamproses');
				$this->statuscount['revisi'] 		= $rs->value('revisi');
				$this->statuscount['belumbayar'] 	= $rs->value('belumbayar');
                                $this->statuscount['confirmed'] 	= $rs->value('confirmed');
				$this->statuscount['telahbayar'] 	= $rs->value('telahbayar');
				$this->statuscount['siap'] 		= $rs->value('siap');
				$this->statuscount['delivered'] 	= $rs->value('delivered');
			}
			$rs->close();
                        
                        $sql = 'select bcvalidate from BCTable with (nolock)';
			$sql.= ' where kodebc = ' . $this->queryvalue($this->userid());  
			$rs = $this->db->query($sql);
			$this->showneworders = ($rs->fetch() && $rs->value('bcvalidate') > 0);  
                        $rs->close();
                        
			
			$sql = 'select * from vw_onlineorder ';
			$sql.= ' where kodebc = ' . $this->queryvalue($this->userid());  
			//$sql.= ' and (cleardate is null AND Status != ' . $this->sysparam['salesstatus']['clear'] . ')'; //sudah include dalam view
			$sql.= ' order by orderdate desc';
			
			$rs = $this->db->query($sql);
			$count1 = 0;
			$count3 = 0;
			$count2 = 0;
			
			while($rs->fetch())
			{				
				
				switch($rs->value('status'))
				{	
					case $this->sysparam['salesstatus']['ordered']:
						$this->neworders[$count1]['salesid'] = $rs->value('salesid');
						$this->neworders[$count1]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
						$this->neworders[$count1]['kodemember'] = $rs->value('kodemember');
						$this->neworders[$count1]['member'] = '#'.$rs->value('kodemember').' '.$rs->value('namamember');
						$this->neworders[$count1]['totalbayar'] = $rs->value('totalbayar');
						$this->neworders[$count1]['timeleft'] = $rs->value('timeleft');
						$this->neworders[$count1]['statuscode'] = $rs->value('status');
						$count1++;
						break;

					case $this->sysparam['salesstatus']['bypassed'] :
					case $this->sysparam['salesstatus']['inprogress'] :
					case $this->sysparam['salesstatus']['edited'] :
					case $this->sysparam['salesstatus']['validated'] :
					case $this->sysparam['salesstatus']['confirmed'] :
					case $this->sysparam['salesstatus']['cancelled'] :
						$this->orderstofollowup[$count2]['salesid'] = $rs->value('salesid');
						if (is_null($rs->value('orderdate')))
							$this->orderstofollowup[$count2]['orderdate'] = $this->valuedatetime($rs->value('createddate'));
						else
							$this->orderstofollowup[$count2]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));						
						$this->orderstofollowup[$count2]['kodemember'] = $rs->value('kodemember');
						$this->orderstofollowup[$count2]['member'] = '#'.$rs->value('kodemember').' '.$rs->value('namamember');
						$this->orderstofollowup[$count2]['totalbayar'] = $rs->value('totalbayar');
						$this->orderstofollowup[$count2]['userstatus'] = $rs->value('userstatus');
						$this->orderstofollowup[$count2]['statuscode'] = $rs->value('syncstatuscode');
						$this->orderstofollowup[$count2]['statuscode'] = $rs->value('status');
						$count2++;
						break;
						
					default:
						$this->orderstodeliver[$count3]['salesid'] = $rs->value('salesid');
						$this->orderstodeliver[$count3]['orderdate'] = $this->valuedatetime($rs->value('orderdate'));
						$this->orderstodeliver[$count3]['kodemember'] = $rs->value('kodemember');
						$this->orderstodeliver[$count3]['member'] = '#'.$rs->value('kodemember').' '.$rs->value('namamember');
						$this->orderstodeliver[$count3]['totalbayar'] = $rs->value('totalbayar');
						$this->orderstodeliver[$count3]['userstatus'] = $rs->value('userstatus');
						$this->orderstodeliver[$count3]['statuscode'] = $rs->value('status');
						$count3++;
						break;	
				}
			}
			$rs->close();
		}
		
		function neworders()
		{
			$ret = '';
			if (is_array($this->neworders)) 
			{
				$i=0;
				foreach ($this->neworders as $orders)
				{
                                        $url = ($orders['timeleft'] <= 0) ? "" : "bcvieworder.php?backpage=1&salesid=" . urlencode($orders['salesid']);
					$ret.= $i%2?"<tr class=\"pinkrow\">":"<tr>";
					$ret.= '<td align="center"><a href="' . $url . '"><div class="color' . $this->colorstatus($orders['statuscode']) . '"></a></td>';
					$ret.= '<td align="left"><a href="' . $url . '">'. $orders['salesid'] . '</a></td>';
					$ret.= '<td align="left">' . $orders['orderdate'] . '</td>';
					$ret.= '<td align="left">' . $orders['member'] . '</td>';
					$ret.= '<td align="right">' . $this->valuenumber($orders['totalbayar']) . '</td>';
					$ret.= '<td align="right">' . $orders['timeleft'] . '</td>';
					$ret.= '</tr>';
					$i++;
				}
			} else {
				$ret.= '<tr><td colspan="6" align="center">no new order</td></tr>';
			}
			return $ret;
		}
		
		function orderstofollowup()
		{
			$ret = '';
			if (is_array($this->orderstofollowup)) 
			{
				$i=0;
				foreach ($this->orderstofollowup as $orders)
				{
					$ret.= $i%2?"<tr class=\"pinkrow\">":"<tr>";		
					$ret.= '<td align="center"><a href="bcvieworder.php?salesid=' . urlencode($orders['salesid']) . '"><div class="color' . $this->colorstatus($orders['statuscode']) . '"></a></td>';
					$ret.= '<td align="left"><a href="bcvieworder.php?salesid=' . urlencode($orders['salesid']) . '">'. $orders['salesid'] . '</a></td>';
					$ret.= "<td align=\"left\">" . $orders['orderdate'] . "</td>";
					$ret.= "<td align=\"left\">" . $orders['member'] . "</td>";
					$ret.= "<td align=\"right\">" . $this->valuenumber($orders['totalbayar']) . "</td>";
					$ret.= "<td align=\"center\"";
					if ( $orders['statuscode'] == 0 ) $ret.= " class=\"pink\"";
					$ret.= ">" . $orders['userstatus'] . "</td>";
					$ret.= "</tr>";
					
					$i++;
				}
			} else {
				$ret.= '<tr><td colspan="6" align="center">no order to follow up</td></tr>';
			}		
			return $ret;			
		}
		
		function orderstodeliver()
		{
			$ret = '';
			if (is_array($this->orderstodeliver)) 
			{
				$i=0;
				foreach ($this->orderstodeliver as $orders)
				{
					$ret.= $i%2?"<tr class=\"pinkrow\">":"<tr>";
					$ret.= '<td align="center"><a href="bcvieworder.php?salesid=' . urlencode($orders['salesid']) . '"><div class="color' . $this->colorstatus($orders['statuscode']) . '"></a></td>';
					$ret.= '<td align="left"><a ' . ($i%2?'class="grid"':'') . ' href="bcvieworder.php?salesid=' . urlencode($orders['salesid']) . '">'. $orders['salesid'] . '</a></td>';				
					$ret.= "<td align=\"left\">" . $orders['orderdate'] . "</td>";
					$ret.= "<td align=\"left\">" . $orders['member'] . "</td>";
					$ret.= "<td align=\"right\">" . $this->valuenumber($orders['totalbayar']) . "</td>";
					$ret.= "<td align=\"center\">" . $orders['userstatus'] . "</td>";
					$ret.= "</tr>";
					$i++;
				}
			} else {
				$ret.= '<tr><td colspan="6" align="center">no order to deliver</td></tr>';
			}
			return $ret;
		}
	}
?>