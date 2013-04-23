 <?
	include "mbrorder.php";
	class mbrordercheck extends mbrorder
	{	
		
		function run() 
		{	
			parent::run();				
			
			switch($this->action)
			{	
				case "confirmorder":
					$this->confirmorder();				
					break;
				case "orderbaru":
					$this->orderbaru();
					break;
				case "refresh":
					$this->refresh();
					break;
				case "tambah":
					$this->savebc();
					header('location:mbrpilihitem.php?salesid=' . $this->salesid);
					break;
				case "none":
					$this->checksalesopenorder();
					break;
			}
			$this->loaddata();
		}		
		
		function confirmorder()
		{
			$this->refresh();
			if ( $this->isvaliddata() )
			{				
				header('location:mbrpaymentmethod.php?salesid=' . $this->salesid);
				exit;
			}
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
		
		function refresh()
		{
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
		
		function getbc()
		{
			if (!isset($this->param["bc"])) 
			{	
				$sql = "select kodebc from vw_BCMapping where KodeMember = " . $this->queryvalue($this->userid());
				$sql.= " and defaultbc = 1";
				$this->param["bc"] = $this->db->executeScalar($sql);
				$this->defaultbckode = $this->param["bc"];
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
			$sql = "select top 1 isnull(totalbayar,0) as totalbayar from vw_salestable where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
			    $this->totalbayar = $rs->value('totalbayar'); 
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
			
			//echo $this->totalbayar . '-' .$maxtotalsales . '-' . $mintotalsales;
			if ( $this->totalbayar > $maxtotalsales || $this->totalbayar < $mintotalsales )
			{
			    $this->errmsg = 'Minimum order harus diatas IDR ' . $this->valuenumber($mintotalsales) . ' dan maximum order IDR ' . $this->valuenumber($maxtotalsales);
			    $ret = false;    
			}
			
			return $ret;			
		}

	}
?>