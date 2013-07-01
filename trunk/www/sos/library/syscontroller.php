<?	
	class syscontroller
	{
		var $param;
		var $sysparam;
		var $action;
		var $checklogin;
                var $dev;
		
		function __construct() 
		{	
			// IF false => PRODUCTION, true => DEVELOPMENT
                        $this->dev = false;
                        
                        session_start();
			switch ($_SERVER['REQUEST_METHOD']) {
				case "GET" :
					$this->param = $_GET;					
					break;
				case "POST" :
					$this->param = $_POST;
					break;
			}
			
			$this->checklogin = true; 			
			$this->setsysparam();					
			$this->action = (isset($this->param["pageaction"]) ? $this->param["pageaction"] : "none");
		}
		
		function run() 
		{ 
			if ($this->debug()) 
			{
				echo "action: ".$this->action . "<br>";
				echo "Param: ";
				print_r($this->param);
				echo "<br>Session: ";
				print_r($_SESSION);
			}
		}
		
		function setsysparam() 
		{
			$this->sysparam['log']['file'] = "c:\\folder\\resource.txt";
		}
		
		function __destruct() { }	
		function debug() { return false; }			
		function systemmaintenance() { return false; }			 
		function opendatabaseconnection() { /* inherited */ }		
		function filename() { return basename($_SERVER["PHP_SELF"]); }
		function value($var) { return isset($this->param[$var]) ? $this->htmlvalue($this->param[$var]) : ''; }
		function varvalue($var) { return isset($this->$var) ? $this->htmlvalue($this->$var) : ''; }
		function valuenumber($value,$decimal=0) {return number_format((double)$value,$decimal,',','.'); }
		function valuedatetime($value) {return date('d M Y H:i',strtotime($value)); }
		function htmlvalue($value) { return htmlspecialchars($value); }
		function queryvalue($value) { return "'" . str_replace("'","''",$value) . "'"; }
		function querydatevalue($value,$format="dmy")
		{
			$day = 0;
			$month = 0;
			$year = 0;
			$hour = 0;
			$minute = 0;
			$second = 0;
			
			$set = false;
			$ret = "null";
			
			switch(strtolower($format))
			{
				case "dmy":
					if (strpos($value,' ')) 
						list($day, $month, $year, $hour, $minute, $second) = split('[ :/.-]', $value);
					else
						list($day, $month, $year) = split('[ :/.-]', $value);						
					//list($day, $month, $year) = split('[/.-]', $value);
					$set = true;
					break;
				case "mdy":
					if (strpos($value,' ')) 
						list($month, $day, $year, $hour, $minute, $second) = split('[ :/.-]', $value);
					else
						list($month, $day, $year) = split('[ :/.-]', $value);
					//list($month, $day, $year) = split('[/.-]', $value);
					$set = true;
					break;					
			}
			if ($set)
			{
				if (strpos($value,' ')) 
				{
					$ret = "'" . $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2) . " ";
					$ret.= substr("0".$hour,-2) . ":" . substr("0".$minute,-2) . ":" . substr("0".$second,-2) . "'" ;
				}
				else
					$ret = "'" . $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2) . "'";
			}
			return $ret;
		}
		
		function log($value)
		{
			$handle = fopen($this->sysparam['log']['file'], "a");
			if($handle)
			{
				$timestamp = date('d M Y H:i:s');
				fwrite($handle,$timestamp . " " . $value);
				fclose($handle);
			}
		}
		
		//HTML functions		
		function setselectoption($name, $sql, $colNameValue, $colNameLabel, $selectedvalue) {
			$rs = $this->db->query($sql);
			$first = true;
			while ($rs->fetch())
			{
				$selected = false;
				
				if ($rs->value($colNameValue)== $selectedvalue || ($first && $selectedvalue == ''))
				{					
					$selected = true;
					$this->setselectedoption($name,$rs);
				}				
				echo "<option value=" . $this->htmlvalue($rs->value($colNameValue)) . ($selected ? " selected " : "") .">" . $this->htmlvalue($rs->value($colNameLabel));
				$first = false;
			}
		}		
		function setselectedoption($name,$rs) { /* inherited */ }		
	}
	
?>