<?
	class mbrlogin extends controller
	{	
		function run() 
		{							
                    $this->checklogin = false;	
                    
                    parent::run();
                    
                    if($this->action == "save")
                    {
                        $userid = trim($this->param["username"]);
                        $password = trim($this->param["norekening"]) ;  
                        if (!empty($userid) && empty($password))
                        {
                            $this->errmsg = 'Kode Member dan/atau Nomor Rekening yang Anda masukan salah.';
                        }
                        else
                        {
                            // Import/update member
                            $this->importmember($userid);

                            // Check user/password in SQL
                            $sql = "SELECT count(kodemember) FROM memberTable";
                            $sql.= " WHERE kodemember = '$userid' AND NoREKENING = '$password'";

                            if ($this->db->executescalar($sql) > 0)
                            {
                                $_SESSION[$this->sysparam['session']['userid']] = $userid;
                                $_SESSION[$this->sysparam['session']['usertype']] = $this->usertype;
                                $this->gotohomepage(); 
                            }
                            else
                            {
                                $this->errmsg = "Kodemember Anda belum terdaftar di sistem atau Nomor Rekening <b>$password</b> tidak sesuai dengan Kode Member <b>$userid</b>";
                            }
                        }
                    }

		}		
	}
?>