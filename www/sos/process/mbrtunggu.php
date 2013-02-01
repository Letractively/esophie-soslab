<?
	class mbrtunggu extends controller
	{	
		var $bcno;
		var $bcname;
		var $totalbayar;
		var $salesid;
		
		function run() 
		{			
			parent::run();

			$laststatus = $this->getlaststatus();
			switch($laststatus)
			{
				case $this->sysparam['salesstatus']['ordered']		:
				case $this->sysparam['salesstatus']['bypassed']		:
				case $this->sysparam['salesstatus']['inprogress']	: break;
				default												: echo'tes'; $this->gotolastpage($laststatus); break;
			}
						
			$this->salesid = $this->salesid();
			$sql = "select * from vw_salesTable";
			$sql.= " where salesid = " . $this->queryvalue($this->salesid);
			$rs = $this->db->query($sql);			
			if ($rs->fetch()) 
			{
				$this->bcno = $rs->value("kodebc");
				$this->bcname = $rs->value("namabc");
				$this->totalbayar = $rs->value("totalbayar");
			}
			$rs->close();
		}
	}
?>