<?
	class mbrlogin extends controller
	{	
		function run() 
		{							
                    $this->checklogin = false;	
                    
                    parent::run();
                    
                    // GOOGLE ANALYTICS PAGE TRACKING
                    $this->gapage = "/member/login";
                    $this->gatitle = "Order - Member - Login page";
                    // GOOGLE ANALYTICS PAGE TRACKING
                    
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
                            $adminUser = "";
                            $userarray = explode('@',$userid, 2);
                            if ($userarray && sizeof($userarray) > 0)
                            {
                                $userid = $userarray[0];
                                $adminUser = (sizeof($userarray) > 1) ? $userarray[1] : "";
                            }

                            // Import/update member
                            $this->importmember($userid);

                            // Check user/password in SQL
                            $sql = "SELECT count(kodemember) FROM memberTable";
                            $sql.= " WHERE kodemember = '$userid' ";
                                    
                            if (strlen($adminUser) > 0) 
                            {
                                $sql.= " and exists (select 1 from BCTable ";
                                $sql.= " where kodebc = ". $this->queryvalue($adminUser) . " and password = " . $this->queryvalue(md5($password)) . ")";
                            }
                            else
                            {
                                $sql.= " AND NoREKENING = '$password'";
                            }

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