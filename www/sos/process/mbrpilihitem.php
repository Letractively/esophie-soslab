<?
	class mbrpilihitem extends controller
	{
		var $maxitem;
		
		function run() 
		{
			$this->maxitem = 5;
			$this->salesid = isset($this->param['salesid']) ? $this->param['salesid'] : '';
			
			parent::run();

			switch($this->action)
			{			
				case "save" :	
					if ($this->isvaliddata())
						$this->savedata();
					break;
				case "reset":
					$this->reset ();		
					break;
				case "none" :
					if (isset($this->param['salesid']))
					{
						$this->salesid = $this->param['salesid'];
						$sql = 'select count(*) from salestable ';
						$sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
						$sql.= ' and status = ' . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
						$sql.= ' and salesid = ' . $this->queryvalue($this->salesid);
						
						if(!$this->db->executeScalar($sql)) $this->gotohomepage();					
					}
					else
					{
						$sql1 = 'select count(*) from salestable ';
						$sql1.= ' where kodemember = ' . $this->queryvalue($this->userid());
						$sql1.= ' and status < ' . $this->sysparam['salesstatus']['confirmed'];
						$sql1.= ' and status > 1';

						if($this->db->executeScalar($sql1)) $this->gotohomepage();		
											
						$sql2 = 'select salesid from salestable ';
						$sql2.= ' where kodemember = ' . $this->queryvalue($this->userid());
						$sql2.= ' and status = ' . $this->sysparam['salesstatus']['openorder'];
						
						$rs = $this->db->query($sql2);			
						if ($rs->fetch()) 
						{
							$this->param['salesid'] = $rs->value('salesid');
							$this->salesid = $rs->value('salesid');
                                                        $this->gotopage('checkitem',"salesid=" . $this->salesid);
						}
						//else $this->gotohomepage();
					}
					break;
			}			
			//if ($this->salesid == '') $this->gotohomepage();
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
								/*
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
		
		function reset () 
		{
			for ($i=1;$i<=$this->maxitem;$i++)
			{
				$this->param["item".$i] = '';
				$this->param["item".$i."qty"] = '';
			}
		}
		
		function savedata() 
		{					
			if ($this->salesid == '')
			{
				$sql = "exec sp_getNextNo 'SALES'";				
				$this->salesid = $this->db->executeScalar($sql);
				$this->param['salesid'] = $this->salesid;
			}	
			
			$dataupdate = false;
			for ($i=1;$i<=$this->maxitem;$i++)
			{
				if ($this->param["item".$i] != '') 
				{
					$dataupdate = true;
					$sql = "exec sp_updateSalesLine " . $this->queryvalue($this->salesid) . "," . $this->queryvalue($this->param["item".$i]) . "," . $this->param["item".$i."qty"];				
					$this->db->execute($sql);
				}
			}
			
			if ($dataupdate)
			{			
				$sql = 'select top 1 name, phone, email from membertable where kodemember = ' . $this->queryvalue($this->userid());
				$rs = $this->db->query($sql);
				if ($rs->fetch()) 
				{
					$namamember = $rs->value('name');
					$mobilemember = $rs->value('phone');
					$emailmember = $rs->value('email');
				}
				$rs->close();
					
				$sql = 'insert into salesTable ';
				$sql.= '(salesid, kodemember, namamember, telp, email, status, createddate) values (';
				$sql.= $this->queryvalue($this->salesid) . ',' ;
				$sql.= $this->queryvalue($this->userid()) . ',' ;
				$sql.= $this->queryvalue($namamember) . ',' ;		
				$sql.= $this->queryvalue($mobilemember) . ',' ;		
				$sql.= $this->queryvalue($emailmember) . ',' ;		
				$sql.= $this->sysparam['salesstatus']['openorder'] . ',getdate())' ;			
				
				$this->db->execute($sql);
			
				$sql = "exec sp_updateSalesTotal " . $this->queryvalue($this->salesid);					
				$this->db->execute($sql);					
			}

			$sql = "select count(itemid) as total from salesline ";
			$sql.= " where salesid = " . $this->queryvalue($this->salesid);
			
			if (!$this->db->executeScalar($sql))
			{
				$this->param["errmsg"] = "Silahkan isi item yang ingin di pesan.";
			}
			else
			{ 
                            $this->gotopage('checkitem', "salesid=".urlencode($this->salesid));
			}
		}
	}
?>
