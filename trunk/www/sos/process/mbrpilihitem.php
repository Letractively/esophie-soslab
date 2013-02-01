<?
	class mbrpilihitem extends controller
	{
		var $maxitem;
	
		function run() 
		{
			$this->maxitem = 5;
			
			parent::run();
			
			switch($this->action)
			{			
				case "save" :			
					if ($this->isvaliddata())
						$this->savedata();
					break;
				case "none" :
					$laststatus = $this->getlaststatus();
					if ($laststatus != $this->sysparam['salesstatus']['openorder'])
						$this->gotolastpage($laststatus);
					break;
			}			
		}
		
		function isvaliddata()
		{
			$ret = true;
					
			for ($i=1;$i<=$this->maxitem;$i++)
			{
				$errname = "item".$i."err";
				$this->param[$errname] = '';
				if ($this->param["item".$i] != '') 
				{					
					$sql = "select count(itemid) as total from inventTable with (nolock)";
					$sql.= " where itemid = " . $this->queryvalue($this->param["item".$i]);
					$sql.= " and deadstyle = 0";

					if ( !$this->db->executeScalar($sql) )
					{
						$this->param[$errname] = 'kode item tidak ada';
					}
				        else
					{	
						if ($this->param["item".$i."qty"] == '')		
						{
							if ($this->param[$errname] == '')
								$this->param[$errname] = "quantity harus di isi";										
						}
						else
						{
							if (!is_numeric($this->param["item".$i."qty"]))
							{
								$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
								$this->param[$errname] .= "quantity harus numeric";										
							}
							else
							{
								if (floatval($this->param["item".$i."qty"]) <= 0)
								{
									$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
									$this->param[$errname] .= "quantity harus lebih besar dari 0";										
								}
								/* Desactivate because no stock checking on mbr interface
								else
								{
									$sql = "exec sp_checkQuantity" . $this->queryvalue($this->param["item".$i]);
									$qtyStock = $this->db->executeScalar($sql);
									$qtyOrder = $this->param["item".$i."qty"];
									if ($qtyStock - $qtyOrder < 0 )
									{
										$this->param[$errname] = 'stock item tidak mencukupi';
									}						
								}
								*/
							}
						}
					}
				}
				else
				{			
					if (!$this->param["item".$i."qty"] == '')	
					{
						$this->param[$errname] = "kode item tidak boleh kosong";
						
						if (!is_numeric($this->param["item".$i."qty"]))
						{
							$this->param[$errname] .= ($this->param[$errname] ? " dan " : "");
							$this->param[$errname] .= "quantity harus numeric";
						}
						else
						{
							if (floatval($this->param["item".$i."qty"]) <= 0)
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
		
		function savedata() 
		{					
			$salesid = $this->salesid();
	
			$dataupdate = false;
			for ($i=1;$i<=$this->maxitem;$i++)
			{
				if ($this->param["item".$i] != '') 
				{
					$dataupdate = true;
					$sql = "exec sp_updateSalesLine " . $this->queryvalue($salesid) . "," . $this->queryvalue($this->param["item".$i]) . "," . $this->param["item".$i."qty"];				
					$this->db->execute($sql);
				}
			}
			
			if ($dataupdate)
			{					
				$sql = "exec sp_updateSalesTotal " . $this->queryvalue($salesid);					
				$this->db->execute($sql);					
			}

			$sql = "select count(itemid) as total from salesline ";
			$sql.= " where salesid = " . $this->queryvalue($salesid);
			
			if (!$this->db->executeScalar($sql))
			{
				$this->param["errmsg"] = "Silahkan isi item yang ingin di pesan.";
			}
			else
			{
				$this->gotopage('checkitem');
			}
		}
	}
?>
