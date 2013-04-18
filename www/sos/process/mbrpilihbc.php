<?
	class mbrpilihbc extends controller
	{
		var $bcno;
		var $bcname;
		var $bcaddress;
		var $bcphone;
				
		function run() 
		{
			parent::run();	
					
			switch($this->action)
			{
				case "back":
					$this->backpage();
					break;
				case "save":
					$this->savedata();
					break;
				case "none":					
					$laststatus = $this->getlaststatus();
					if ($laststatus != $this->sysparam['salesstatus']['openorder'])
						$this->gotolastpage($laststatus);
					break;
			}
		}
				
		function savedata() 
		{
			$salesid = $this->salesid();
			
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
			$sql.= " where salesid = " . $this->queryvalue($salesid);			

			$this->db->execute($sql);	
			
			$this->gotopage('inputitem');
		}
		
		function backpage() 
		{
			$this->gotopage('memberinfo');
		}
		
		function getbc()
		{
			if (!isset($this->param["bc"])) 
			{	
				$sql = "select kodebc from vw_BCMapping where KodeMember = " . $this->queryvalue($this->userid());
				$sql.= " and defaultbc = 1";
				$this->param["bc"] = $this->db->executeScalar($sql);
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
		
	}
?>