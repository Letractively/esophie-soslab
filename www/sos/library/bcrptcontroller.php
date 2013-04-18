<?
	include_once "bccontroller.php";
	
	class rptcontroller extends controller
	{			
		var $items;
		var $sortby;
		var $sortorder;
		
		function debug() { return false; }	
		
		function run()
		{			
			parent::run();
			$this->sortby = isset($this->param["sortby"]) ? $this->param["sortby"] : '';
			$this->sortorder = isset($this->param["sortorder"]) ? $this->param["sortorder"] : 'asc';
			$this->setsortcolumn();
		}
		
		function setsortcolumn()
		{
			$key = "sortby_";
			if (strtolower(substr($this->action,0,7)) == $key)
			{
				
				$column = str_replace($key, "", strtolower($this->action));
				
				if(strtolower($this->sortby) == $column)
				{						
					$this->sortorder = ($this->sortorder == "asc" ? "desc" : "asc");
				}
				else 
				{
					$this->sortorder ="asc";
				}				
				$this->sortby = $column;
			}
		}
		
		function sortimage($colname) 
		{
			$ret = '';
			if ($colname == $this->sortby)
			{
				if ($this->sortorder == "asc")
				{
					$ret = "<img src=\"images/asc.gif\">";
				}
				else
				{
					$ret = "<img src=\"images/desc.gif\">";
				}
			}
			return $ret;
		}
	}
?>