<?
	class mbrneworder extends controller
	{	
		function run() 
		{				
			parent::run();
			
			$sql = 'update salestable set ';
			$sql.= ' status = 0';
			$sql.= ' ,canceldate = getdate()';
			$sql.= ' ,cancelcode = ' . $this->sysparam['cancelcode']['bymember'];
			$sql.= ' from salestable ';
			$sql.= ' where kodemember = ' . $this->queryvalue($this->userid());
			$sql.= ' and status = ' . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
			$this->db->execute($sql);
			
			header("location:mbrpilihitem.php");
		}		
	}
?>