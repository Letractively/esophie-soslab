<?
	class mbrlogin extends controller
	{	
		function run() 
		{							
                    $this->checklogin = false;	
                    
                    parent::run();
                    
                    if($this->action == "save")
                    {
                        $userid = $this->param["username"];
                        $password = $this->param["norekening"] ;  
                        if (!empty($userid) && empty($password))
                        {
                            $this->errmsg = 'Kode Member dan/atau Nomor Rekening yang Anda masukan salah.';
                        }
                        else
                        {
                            $kdmember = str_pad($_POST['username'], 10, " ", STR_PAD_LEFT); //warning: the member id is filled with blank spaces at the begining if shorter than 10 digits
                            // Import/update member
                            $this->importmember($kdmember);

                            // Check user/password in SQL
                            $sql = "SELECT count(kodemember) FROM memberTable";
                            $sql.= " WHERE kodemember = '$kdmember' AND NoREKENING = '$password'";

                            if ($this->db->executescalar($sql) > 0)
                            {
                                $_SESSION[$this->sysparam['session']['userid']] = $userid;
                                $_SESSION[$this->sysparam['session']['usertype']] = $this->usertype;
                                $this->checkmembermapping();
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