<?
	class mbrneworder extends controller
	{	
		function run() 
		{							
			parent::run();
			$sql = 'select salesid from salestable ';
			$sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
			$sql.= ' and status = ' . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
						
			$this->salesid = $this->db->executescalar($sql);
			if (!is_null($this->salesid))
			{
				$sql = 'delete salesline where salesid = ' . $this->queryvalue($this->salesid);
				$this->db->execute($sql);
				$sql = 'delete salestable where salesid = ' . $this->queryvalue($this->salesid);
				$this->db->execute($sql);
			}
			
			/*
			$sql = 'update salestable set ';
			$sql.= ' status = 0';
			$sql.= ' ,canceldate = getdate()';
			$sql.= ' ,cancelcode = ' . $this->sysparam['cancelcode']['bymember'];
			$sql.= ' from salestable ';
			$sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
			$sql.= ' and status = ' . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
			$this->db->execute($sql);
			*/
			header("location:mbrpilihitem.php");
		}		
	}
?>