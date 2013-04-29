<?php
	include_once "library/database.php";
	include_once "library/emailsmscontroller.php";
	include_once "library/class.phpmailer.php";

	$sql = "";
	$sqlUpdate = "";
	$varBody = "";
	$smsurl = "";
	$status = -1;
	$msgid = "";
	$phonenumber = "";
	$smsurl = "http://broadcast.jatismobile.com/smspush/send.aspx?userid=smartin&password=smartin123";

	$execute = new emailsmscontroller();
			
	$query = new emailsmscontroller();
	$sql = "select [noseq], isnull(phone,'') as phone, Replace(message,' ','%20') as message from smsTable with (NOLOCK) where sendDate is null and isnull(retrynumber,0) < 3";
	$rs	= $query->db->query($sql);
	while ( $rs->fetch() )
	{
		try 
		{
			$phonenumber = $rs->value('phone');
			if ( $phonenumber != '' )
			{
				if ( substr($phonenumber,0,1) == '0' )
					$phonenumber = '62' . substr($phonenumber,1,strlen($phonenumber)-1);
				if ( substr($phonenumber,0,2) != '62' )
					$phonenumber = '62' . $phonenumber;
				
				$smsurlsent = $smsurl . '&message=' . $rs->value('message') . '&msisdn=' . $phonenumber;
				//echo $smsurlsent.'<br>';
				
				$result = file_get_contents($smsurlsent);
				$resArray = explode("&",$result);
				
				//echo $resArray[0].'<br>';
				if ( $resArray[0] != '' )
				{
					$temp = explode("=",$resArray[0]);
					if ( count($temp) > 0 )
						$status = $temp[1];
				}	
			
				//echo $resArray[1].'<br>';
				if ( $resArray[1] != '' )
				{
					$temp = explode("=",$resArray[1]);
					if ( count($temp) > 0 )
						$msgid = $temp[1];
				}
				
				//echo $status . "-" . $msgid; 
				if ( $status == '0' )
				{
					$sqlUpdate = "update smsTable set sendDate=getdate(), messageid='" . $msgid . "' where noseq =" . $rs->value('noseq');
					$execute->db->execute($sqlUpdate);
				}
				else
				{
					$sqlUpdate = "update smsTable set retrynumber = isnull(retrynumber,0) + 1 where noseq =" . $rs->value('noseq');
					$execute->db->execute($sqlUpdate);
				}
				
			}
		} 
		catch (Exception $e) 
		{
			echo $e->getMessage();			
		}
	}
	$rs->close();
?>

