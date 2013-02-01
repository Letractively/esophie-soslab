<?php
	include_once "library/database.php";
	include_once "library/emailsmscontroller.php";
	include_once "library/class.phpmailer.php";

	$sql = ""; 
	$sqlUpdate = "";
	$varBody = "";
	
	$execute = new emailsmscontroller;
	
	$query = new emailsmscontroller;
	$sql = "select top 30 [noseq],[from],[to],isnull([cc],'') as cc,isnull([bcc],'') as bcc,[subject],[body],[createdDate],[sendDate],isnull([toname],[to]) as toname from emailTable with (NOLOCK) where sendDate is null";
	$rs	= $query->db->query($sql);
	while ( $rs->fetch() )
	{
		try 
		{
			$mail = new PHPMailer(true); 	// the true param means it will throw exceptions on errors, which we need to catch
			$mail->IsSMTP(); 				// telling the class to use SMTP

			// mail property
			$mail->Host       = "10.0.0.17"; 	// SMTP server
			//$mail->SMTPAuth   = true;                  		// enable SMTP authentication
			$mail->Port       = 25;                    	// set the SMTP port for the GMAIL server
			$mail->Username   = "victor@sophieparis.com"; 		// SMTP account username

			// Mail Address
			$mail->SetFrom('victor@sophieparis.com', 'SOS (NO REPLY)');
			$mail->AddAddress( trim($rs->value('to')), trim($rs->value('toname')) );
			if ( trim($rs->value('cc')) != '' )
				$mail->AddCC(trim($rs->value('cc')));

			if ( trim($rs->value('bcc')) != '' )
				$mail->AddBCC(trim($rs->value('bcc')));	

			// Mail Message
			$varBody =
				'<body style="margin: 10px;">
				<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">'
				. $rs->value('body') . 
				'<br><br>
				Regards,<br><br>
				<b>Sophie Martin Indonesia</b>
				</div>
				<div align="left"><img src="images/logo.gif" style="height: 90px; width: 340px"></div><br>
				</body>';
			$mail->Subject = $rs->value('subject');
			$mail->MsgHTML($varBody);

			$mail->Send();
			
			$sqlUpdate = "update emailtable set sendDate=getdate() where noseq =" . $rs->value('noseq');
			$execute->db->execute($sqlUpdate);
		} 
		catch (Exception $e) 
		{
			echo $e->getMessage();
		}
	}
	$rs->close();
?>

</body>
</html>
