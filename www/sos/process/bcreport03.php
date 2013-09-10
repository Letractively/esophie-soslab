<?
	class bcreport03 extends rptcontroller
	{			
		var $searchcriteria;
		var $totalkredit;
		var $where;
		function run() 
		{				
			parent::run();
                        
                        // GOOGLE ANALYTICS PAGE TRACKING
                        $this->gapage = "/bc/report/creditbc";
                        $this->gatitle = "Order - BC - Report Credit BC";
                        // GOOGLE ANALYTICS PAGE TRACKING
                        
			switch($this->action)
			{
				case 'none':
					$this->sortby = 'salesid';
					$this->param['search_paiddate_from'] = date('d/m/Y');
					$this->param['search_paiddate_to'] = date('d/m/Y');
					break;
				case 'reset':
					unset($this->param);
					break;
			}
			
			if ($this->sortby == '') $this->sortby = 'salesid';
			if (!isset($this->param['search_paiddate_from'])) $this->param['search_paiddate_from'] = date('d/m/Y');
			if (!isset($this->param['search_paiddate_to'])) $this->param['search_paiddate_to'] = date('d/m/Y');
			
			$this->loaddata();
		}		
		
		function loaddata() 
		{
			$this->searchcriteria = '';
			$this->where = '';
			
			$sql = 'select * from vw_report03 ';
			$sql.= ' where kodebc = ' . $this->queryvalue($this->userid());  			
			//$sql.= ((isset($this->param['search_salesid']) && trim($this->param['search_salesid']) != '') ? ' and salesid = ' . $this->queryvalue($this->param['search_salesid']) : '' );
			//$sql.= ((isset($this->param['search_kodemember']) && trim($this->param['search_kodemember']) != '') ? ' and kodemember = ' . $this->queryvalue($this->param['search_kodemember']) : '' );			
			//$sql.= ((isset($this->param['search_namamember']) && trim($this->param['search_namamember']) != '') ? ' and namamember like '%' . str_replace(''','''',$this->param['search_namamember']) . '%'' : '' );			
			//$sql.= ((isset($this->param['search_status']) && trim($this->param['search_status']) != '') ? ' and status = ' . $this->param['search_status'] : '' );
			//$sql.= ((isset($this->param['search_salesidsmi']) && trim($this->param['search_salesidsmi']) != '') ? ' and salesidsmi = ' . $this->queryvalue($this->param['search_salesidsmi']) : '' );
			if (isset($this->param['search_paiddate_from']) && trim($this->param['search_paiddate_from']) != '')
			{
				$this->where.= ' and paiddate >= ' . $this->querydatevalue($this->param['search_paiddate_from']);
				$this->searchcriteria .= ($this->searchcriteria != '' ? ';' : '') . 'search_paiddate_from:'. $this->param['search_paiddate_from'];
			}
			if (isset($this->param['search_paiddate_to']) && trim($this->param['search_paiddate_to']) != '')
			{
				$this->where.= ' and paiddate <= ' . $this->querydatevalue($this->param['search_paiddate_to']. ' 23:59:59');
				$this->searchcriteria .= ($this->searchcriteria != '' ? ';' : '') . 'search_paiddate_to:'. $this->param['search_paiddate_to'];
			}					
			
			$sql.= $this->where . ' order by ' . $this->sortby . ' ' .$this->sortorder ;

			//echo $sql;
			
			$rs = $this->db->query($sql);
			$count = 0;
			
			while($rs->fetch())
			{			
				$this->items[$count]['salesid'] = $rs->value('salesid');				
				$this->items[$count]['paiddate'] = (is_null($rs->value('paiddate')) ? '-' : $this->valuedatetime($rs->value('paiddate')));
				$this->items[$count]['kodemember'] = $rs->value('kodemember');
				//$this->items[$count]['namamember'] = $rs->value('namamember');
				$this->items[$count]['totalbayarmbr'] = $rs->value('totalbayarmbr');
				$this->items[$count]['salesidsmi'] = $rs->value('salesidsmi');
				$this->items[$count]['paymentcharge'] = $rs->value('paymentcharge');
				$this->items[$count]['totalbayarbc'] = $rs->value('totalbayarbc');
				$this->items[$count]['kreditbc'] = $rs->value('kreditbc');
				$this->items[$count]['statusname'] = $rs->value('statusname');
				$this->items[$count]['status'] = $rs->value('status');
				$count++;				
			}
			$rs->close();
			
			$sql = 'select isnull(sum(kreditbc),0) as kreditbc from vw_report03 ';
			$sql.= ' where kodebc = ' . $this->queryvalue($this->userid());  
			$sql.= $this->where;
			
			$this->totalkredit = $this->db->executescalar($sql);
		}
		
		
	}
?>