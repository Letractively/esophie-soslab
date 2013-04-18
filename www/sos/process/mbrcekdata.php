<?
	class mbrcekdata extends controller
	{
		function run() 
		{
			parent::run();
			if ($this->debug()) echo '<br>Action :' . $this->action;
			switch($this->action)
			{
				case "ok"	:
					if ( $this->validateData() )
						$this->savedata();
					break;
				case "none" :
					//note: member dapat kembali ke page ini dengan mengetik url
					//      selama order status yang terakhir masih open order					
					$laststatus = $this->getlaststatus();
					if ($this->debug()) echo "laststatus:" .$laststatus;
					
					if ($laststatus != $this->sysparam['memberstatus']['neworder'] &&
					    $laststatus != $this->sysparam['salesstatus']['openorder']) 
						$this->gotolastpage($laststatus);
					
					$this->loaddata();
					break;
			}
		}
		
		function loadData() 
		{
			//buka blok coding dibawah bila mau mengambil data dari order terakhir di sales table dengan status openorder
			/*
			//note: hanya ada 1 salesid per member dengan status openorder			
			$sql = "select kodemember, namamember, alamat, telp, email from salesTable ";
			$sql.= " where kodemember = " . $this->queryvalue($this->userid());
			$sql.= " and status = " . $this->queryvalue($this->sysparam['salesstatus']['openorder']);
			
			$rs = $this->db->query($sql);
			if ($rs->fetch())
			{
				$this->param["memberno"] = $rs->value("kodemember");
				$this->param["nama"] = $rs->value("namamember");
				$this->param["alamat"] = $rs->value("alamat");
				$this->param["handphone"] = $rs->value("telp");
				$this->param["email"] = $rs->value("email");
			}
			else
			{
			*/
				$sql = "select kodemember, namamember, alamat, telp, email from vw_member ";
				$sql.= " where kodemember = " . $this->queryvalue($this->userid());
				$rs1 = $this->db->query($sql);
				if ($rs1->fetch())
				{
					$this->param["memberno"] = $rs1->value("kodemember");
					$this->param["nama"] = $rs1->value("namamember");
					$this->param["alamat"] = $rs1->value("alamat");
					$this->param["handphone"] = $rs1->value("telp");
					$this->param["email"] = $rs1->value("email");
				}
				$rs1->close();
			/*
			}
			$rs->close();*/
		}
		
		function savedata() 
		{			
			$salesid = $this->salesid();
			
			if ($salesid == '')
			{
				$sql = "exec sp_getNextNo 'SALES'";				
				$salesid = $this->db->executeScalar($sql);
						
				$sql = "insert into salesTable ";
				$sql.= "(salesid, kodemember, namamember, alamat, telp, email, status) values (";
				$sql.= $this->queryvalue($salesid) . "," ;
				$sql.= $this->queryvalue($this->userid()) . "," ;
				$sql.= $this->queryvalue($this->param["nama"]) . "," ;
				$sql.= $this->queryvalue($this->param["alamat"]) . "," ;
				$sql.= $this->queryvalue($this->param["handphone"]) . "," ;
				$sql.= $this->queryvalue($this->param["email"]) . "," ;			
				$sql.= $this->sysparam['salesstatus']['openorder'] . ")" ;			
			}
			else
			{			
				$sql = "update salesTable set ";			
				$sql.= "namamember = " . $this->queryvalue($this->param["nama"]);
				$sql.= ",alamat = " . $this->queryvalue($this->param["alamat"]);
				$sql.= ",telp = " . $this->queryvalue($this->param["handphone"]);
				$sql.= ",email = " . $this->queryvalue($this->param["email"]);
				$sql.= " where salesid = " . $this->queryvalue($salesid);			
			}
		
			$this->db->execute($sql);
			$this->gotopage('selectbc');
		}
		
		function validateData ( )
		{
			$mobile = trim($this->param["handphone"]);
			if ( strlen($mobile) <= 0 )
			{
				$this->param["errmsg"] = "Nomor handphone harus diisi";
				return false;
			}
			
			$number = array("0","1","2","3","4","5","6","7","8","9");
			$result = str_replace( $number, "", $mobile );
			if ( strlen($result) > 0 )
			{
				$this->param["errmsg"] = "Nomor handphone harus diisi dengan angka saja";
				return false;
			}

			$email = trim($this->param["email"]);
			if ( strlen($email) <= 0 )
			{
				$this->param["errmsg"] = "Email harus diisi";
				return false;
			}

			if ( strpos($email, "@") <= 0 )
			{
				$this->param["errmsg"] = "Email harus dimasukan dengan benar";
				return false;
			}
			
			return true;			
		}
		
	}
?>