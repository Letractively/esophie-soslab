<?

	class rsMsSQL {
		var $rId;
		var $record;
		var $pagenum;
		var $rowsperpage;
		var $rowscount;
		var $setpage;
		var $maxpage;
		
		function __construct($rowsperpage=25) {
			$this->setpage=false;
			$this->rowsperpage = $rowsperpage;
		}
		function printField(){
			/*
			for($i=0;$i<mssql_num_fields($this->rId);$i++){
				$fieldObj = mssql_fetch_field($this->rId,$i);
				//echo $fieldObj->name."<br>";
			}
			*/
			for($i=0;$i<$this->rId->columnCount();$i++){
				$fieldObj = mssql_fetch_field($this->rId,$i);
				//echo $fieldObj->name."<br>";
			}
		}
		/*
		function fieldName2Num($fieldName){
			for($i=0;$i<mssql_num_fields($this->rId);$i++){
				$fieldObj = mssql_fetch_field($this->rId,$i);
				if (strtolower($fieldName) == strtolower($fieldObj->name)) {
					return $i;
				}	
			}
			return -1;
		}
		*/
		
		function value($idx,$format="") {
			if ($this->record) {
				/*
				if (!is_int($idx)) {
					$idx1 = $idx;
					$idx = $this->fieldName2Num($idx);							
				}
				*/
				/*
				switch(mssql_field_type ($this->rId,$idx)) {
					case "datetime":
						if (is_null($this->record[$idx])) return "";
						$dt = new mydate();
						$dt->dbvalue2date($this->record[$idx]);
						return $dt->getdate($format);
						break;
				}
				*/
				return $this->record[$idx];
			}
			return null;
		}
		function fields() {	
			//return mssql_num_fields($this->rId); 
			return $this->rId->columnCount();
		}
		function rows() {
			//return mssql_num_rows($this->rId); 	
			$this->rId->rowCount();
		}
		/*
		function setpage($pagenum,$rowsperpage=null) {
			$this->rowscount =0;
			$this->pagenum = $pagenum;
			$this->setpage = true;
			$this->maxpage = ceil($this->rows()/$this->rowsperpage);
			if ($this->pagenum>$this->maxpage)$this->pagenum = $this->maxpage;
			if ($this->rows()>0) {
				if (($this->pagenum-1)*$this->rowsperpage > $this->rows()) {
					mssql_data_seek($this->rId,$this->rows()-1);
					mssql_fetch_row($this->rId);
				} else {
					mssql_data_seek($this->rId,($this->pagenum-1)*$this->rowsperpage);
				}
			}
		}
		*/
		/*
		function getDataArr() {
			return mssql_fetch_array($this->rId);
		}
		*/
		function fetch(){
			//$this->record = mssql_fetch_row($this->rId);			
			$this->record = $this->rId->fetch();
			
			if($this->setpage) {
				if ($this->rowscount < $this->rowsperpage) {
					$this->rowscount++;
					return $this->record;
				}
				return false;
			}
			return $this->record;
		}
		function record() {
			return $this->record;
		}
		function fieldName($idx) {
			//return mssql_field_name($this->rId,$idx);
			$this->rId->getColumnMeta[$idx]['name'];
		}
		function fieldType($idx) {
			//return mssql_field_type($this->rId,$idx); 
			$this->rId->getColumnMeta[$idx]['native_type'];
		}
		/*
		function nextResult() {
			return mssql_next_result($this->rId);
		}
		*/
		function close() {
			//return mssql_free_result($this->rId);			
			$this->rId->closeCursor();
			$this->rId = null;
		}
	}

    class MsSQL {
        var $SVRName;
        var $DBName;
        var $DBUser;
        var $DBPassword;
        var $link;
        var $errmsg;
        
        function __construct($serverName,$databaseName,$User,$Password) {
            $this->SVRName    = $serverName;
            $this->DBName     = $databaseName;
            $this->DBUser     = $User;
            $this->DBPassword = $Password;
            $this->open();
        }
       
        function open(){
			try
			{
				$this->link = new PDO("dblib:host=".$this->SVRName.";dbname=".$this->DBName, $this->DBUser, $this->DBPassword);
			}
			catch(PDOException $e) 
			{			 
				die("Unable to open database.<br>Error message:<br><br>$e.");
			}
		}
        		
		function query($sql,$databaseName="") {
			$rs = new rsMsSQL();
			
			$rs->rId= $this->link->query($sql);
			//echo $sql;
			
			/*                        
			if ( strtoupper(mssql_get_last_message()) != strtoupper("Changed database context to '".$this->DBName."'.") )
				//$this->errmsg = mssql_get_last_message();
				$this->errmsg = $this->link->errorInfo()[2];
			*/

			return $rs;
		}
		
		function execute($sql,$databaseName=""){
			/*
			if ($databaseName!="") {
				$this->setDatabase($databaseName);
			}
			
			mssql_query($sql,$this->link);
			*/
			$this->link->query($sql);
                        /*
			if ( strtoupper(mssql_get_last_message()) != strtoupper("Changed database context to '".$this->DBName."'.") )
				//$this->errmsg = mssql_get_last_message();
				$this->errmsg = $this->link->errorInfo()[2];
			*/	
		}
		
		function executeScalar($sql,$databaseName="") 
		{
			$rId = $this->link->query($sql);
			$row = $rId->fetch();
			if( isset($row) ) 
			{
				$value = $row[0];
				$rId->closeCursor();				
				$rId = null;
				return $value;
			}
			$rId->closeCursor();
			$rId = null;
			return "";
		}

		function close() {
			if (isset($this->link))	
				$this->link = null;
		}
		
		function reportError() {
			//$msg = mssql_get_last_message();
			/*
			$msg = $this->link->errorInfo()[2];
			if ($msg) {
				echo $msg;
			}
			*/
		}
    }
?>
