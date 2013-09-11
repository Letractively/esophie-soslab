<?	
        include_once "database.php";
        include_once "maincontroller.php";
	
	class batchcontroller extends maincontroller
	{		
            
                function __construct()
		{
                    $this->setsysparam();
                    $this->opendatabaseconnection();
		}

		function run() 
		{ 
			$this->checklogin = false;
			parent::run();	
                        $this->runBatch($this->action);
		}
                
                function runBatch($action)
                {
                    
                    switch ($action)
                    {
                        case "checksync":
                            $this->syncchecking();
                            break;
                        case "sendemail":
                            $this->sendingemail();
                            break;
                        case "sendsms":
                            $this->sendingsms();
                            break;
                        case "paymchecking":
                            $this->paymchecking();
                            break;
                        case "autobypass":
                            $this->autobypass();
                            break;
                        case "monitor":
                            $this->monitor();
                            break;
                    }
                }
                
                function monitor()
                {
                    $sql = "select sales.salesid,  ";
                    $sql.= "sales.kodemember, (select name from membertable where kodemember = sales.kodemember) AS namemember, ";
                    $sql.= "sales.kodebc, (select namabc from bctable where kodebc = sales.kodebc) AS namebc, ";
                    $sql.= "sales.orderdate, sales.maxvalidatedate, sr.requestid, ISNULL(st.force,0) AS force, ";
                    $sql.= "CASE WHEN sr.[timestamp] IS NULL THEN 0  ";
                    $sql.= "ELSE (dbo.Date2UnixTimeStamp(GETDATE()) - sr.[timestamp]) ";
                    $sql.= "END AS overtime ";
                    $sql.= "from salestable sales with (nolock) ";
                    $sql.= "inner join syncrequest sr with (nolock) ";
                    $sql.= "on sr.[sessionid] = sales.[salesid]  ";
                    $sql.= "inner join syncordertable st with (nolock) ";
                    $sql.= "on st.[sessionid] = sr.[sessionid] ";
                    $sql.= "and st.[timestamp] = sr.[timestamp] ";
                    $sql.= "where sr.[status] = 0 ";
                    $sql.= "and (dbo.Date2UnixTimeStamp(GETDATE()) - sr.[timestamp]) > 180 ";
        
                    $counter = 0;
                    $body = "";
                    $rs	= $this->db->query($sql);
                    while ($rs && $rs->fetch())
                    {
                        $counter++;
                        $body .= "<tr>";
                        $body .= "<td>".$rs->value('salesid')."</td>";
                        $body .= "<td>".$this->valuedatetime($rs->value('orderdate'))."</td>";
                        $body .= "<td>".$rs->value('namemember'). " (".$rs->value('kodemember').")</td>";
                        $body .= "<td>".$rs->value('namebc'). " (".$rs->value('kodebc').")</td>";
                        $body .= "<td>".$this->valuedatetime($rs->value('maxvalidatedate'))."</td>";
                        $body .= "<td>".$rs->value('requestid')."</td>";
                        $body .= "<td>".$rs->value('force')."</td>";
                        $body .= "<td>".$rs->value('overtime')."</td></tr>";
                    }
                    
                    if ($counter > 0)
                    {
                        // Send alert to IT
                        $subject = "[ALERT] Online Orders: " . $counter . " order(s) not processed yet!";
                        $body = "<strong>Server ORDER could be down or the Axapta services down...</strong> Try the following:<ul>
                            <li>Remote to ORDER server</li>
                            <li>Check for errors in Event Viewer/Application</li>
                            <li>Stop the services in the following order: AxCom1 & AxCom2 services > Axapta AOS service > Shutdown COM+ Navision AX</li>
                            <li>Start Axapta AOS service and try to open AX client to test</li>
                            <li>Start AxCom1 & AxCom2 services</li>
                            <li>Verify there is no error in EventViewer/Application</li></ul><br/><br/>
                            <hr><table cellspacing=''1'' cellpadding=''1'' style=''width:100%;''>
                            <tbody style=''font-family: helvetica, sans-serif, arial; font-size: 12px; color:#727274;''>
                            <tr><th>Order</th><th>Date</th><th>Member</th><th>BC</th><th>Max Validate</th><th>Request</th><th>Force</th><th>Overtime</th></tr>"  . $body . "</tbody></table>";
                        $sql2 = "insert into emailtable ";
                        $sql2.= "([from],[to], cc, bcc, subject,body,createdDate,[toname], salesid) values ";
                        $sql2.= "('onlineorders@sophie.com', 'victor@sophieparis.com', 'ITInfra&Opsteam@sophieparis.com', 'onlineorderfollowup@sophieparis.com', ";
                        $sql2.= "'" . $subject . "', '" .$body . "', GETDATE(), 'IT', '') ";
                        $this->db->execute($sql2);
                        echo "[BATCH][".date("Y-m-d H:i:s")."][monitor] ALERT FOR ". $counter ." ORDERS! \n";
                    }
                }
		
		function autobypass() 
		{ 
			$sql = "exec sp_salesByPassed" ;
			$this->db->execute($sql);
                        //echo "[BATCH][".date("Y-m-d H:i:s")."][autobypass] OK\n";
		}
                
                function paymchecking()
		{
                    // Loop through all salestable with status > edited (5) and < paid (8) 
                    // payment maximum delay already past a=> check status and/or cancel/confirm/reconcile
                    $sql = "select t1.salesid, t1.status, t2.paymstatus from salestable t1 with (nolock)";
                    $sql.= " inner join paymenttable t2 with (nolock) on t2.salesid = t1.salesid";
                    $sql.= " where (t2.maxpaiddate IS NULL OR t2.maxpaiddate < GETDATE()) AND ";
                    $sql.= " ((t1.status IN (5,6,7) AND t2.paymstatus IN (0,1,3))";
                    $sql.= " OR (t1.status = 6 AND t2.paymstatus = 2)";
                    $sql.= " OR (t1.status IN (6,7) AND t2.paymstatus = 4))";
                    
                    $rs	= $this->db->query($sql);
                    if ($rs)
                    {
                        $salesrows = $rs->fetchAll();
                        $rs->close();
                        // Loop through each sales and updates its status
                        foreach ($salesrows as $row) 
                        {
                            if (strlen($row['salesid'])>0)
                            {
                                $salesid = $row['salesid'];
                                $paymstatus = $row['paymstatus'];
                                switch ($paymstatus)
                                {
                                    case 2:
                                        // mark as confirmed
                                        $this->updatesalesstatus($salesid, $this->sysparam['salesstatus']['confirmed']);
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][paymchecking][".$row['salesid']."] Payment confirmed\n";
                                        break;
                                    case 4:
                                        // mark as paid
                                        $this->updatesalesstatus($salesid, $this->sysparam['salesstatus']['paid']);
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][paymchecking][".$row['salesid']."] Payment reconciled\n";
                                        break;
                                    default:
                                        // mark as cancelled
                                        $this->updatesalesstatus($salesid, $this->sysparam['salesstatus']['cancelled'],
                                                $this->sysparam['cancelcode']['latepayment']);
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][paymchecking][".$row['salesid']."] Cancelled for non payment\n";
                                        break;
                                }
                            }
                        }
                    }
		}
		
		function syncchecking()
		{
                    // Update purchtable according to syncrequest response
                    $sql1 = "exec sp_checkSyncOrder 'order'";
                    $this->db->execute($sql1);
                    
                    // Execute payment initialization for faspay
                    // Loop through all salestable with status validated (6) 
                    // and paymenttable.paymstatus = none / failed (0/3) and payment still possible
                    $sql = "select t1.salesid from salestable t1 with (nolock)";
                    $sql.= " inner join paymenttable t2 with (nolock) on t1.salesid = t2.salesid";
                    $sql.= " where t1.status = 6 and t2.maxpaymdate > GETDATE() and t2.paymstatus in (0,3)";
                    $rs	= $this->db->query($sql);
                    if ($rs)
                    {
                        $salesrows = $rs->fetchAll();
                        $rs->close();
                        // Loop through each sales and updates its status
                        foreach ($salesrows as $row) 
                        {
                            if (strlen($row['salesid'])>0)
                            {
                                // Send HTTP request to paygate to initialize the payment
                                if ($this->initfaspay($row['salesid']))
                                    echo "[BATCH][". date("Y-m-d H:i:s") ."][syncchecking][". $row['salesid'] ."] Payment initialized\n";
                                else
                                    echo "[BATCH][". date("Y-m-d H:i:s") ."][syncchecking][". $row['salesid'] ."] Not initialized\n";
                            }
                        }
                        
                    } 			
		}
		
		function sendingemail()
		{
                    include_once "class.phpmailer.php";

                    $sqlUpdate = "";
                    $varBody = "";
                    $varLine = "";
                    $salesid = "";
                    $idx = 0;
                    $totalorder = 0;
                    $totaldiscount = 0;
                    $totalbayar = 0;
                    $colspan = 0;

                    $sql = "select top 30 [noseq],[from],[to],isnull([cc],'') as cc,isnull([bcc],'') as bcc,[subject],[body],t1.[createdDate],[sendDate],isnull([toname],[to]) as toname, t1.[salesid] as salesid, t2.status as [salesstatus] ";
                    $sql.= " ,t2.orderdate, t2.kodemember, t2.namamember, t2.telp, isnull(t2.alamat,'') as alamat, t2.kodebc, t2.namabc, t2.alamatbc, t2.telpbc, t2.userstatus, t2.bcsalesorderstatus, t2.totalorder, t2.discount, t2.totalbayar, isnull(t2.paymentcharge,0) as paymentcharge ";
                    $sql.= " from emailTable as t1 with (NOLOCK) left join vw_salestable as t2 with (NOLOCK) on t1.salesid=t2.salesid";
                    $sql.= " where T1.sendDate is null and isnull(T1.retrynumber,0) < 3";   

                    $rs	= $this->db->query($sql);
                    if ($rs)
                    {
                        $rows = $rs->fetchAll();
                        $rs->close();
                        
                        foreach ($rows as $row) 
                        {
                            try 
                            {
                                    $mail = new PHPMailer(true); 	// the true param means it will throw exceptions on errors, which we need to catch
                                    $mail->IsSMTP();                    // telling the class to use SMTP

                                    
                                    // mail property
                                    $mail->Host       = $this->sysparam['email']['host']; 	// SMTP server
                                    $mail->SMTPAuth   = false;                                  // disable SMTP authentication
                                    $mail->Port       = $this->sysparam['email']['port'];       // set the SMTP port 
                                    $mail->Username   = $this->sysparam['email']['fromemail']; 	// SMTP account username

                                    // Mail Address
                                    $mail->SetFrom($this->sysparam['email']['fromemail'], $this->sysparam['email']['fromname']);
                                    $mail->AddAddress( trim($row['to']), trim($row['toname']) );
                                    if ( trim($row['cc']) != '' )
                                            $mail->AddCC(trim($row['cc']));

                                    if ( trim($row['bcc']) != '' )
                                            $mail->AddBCC(trim($row['bcc']));	

                                    // Mail Message
                                    // List item
                                    $varBody = $row['body'];
                                    if (isset($row['salesid'])) $salesid = trim($row['salesid']);  
                                    $paymmode = '';
                                    if ( $salesid !=  "" )
                                    {
                                            $idx = 0;
                                            $totalorder = 0;
                                            $totaldiscount = 0;
                                            $totalbayar = 0;
                                            $varLine = "";
                                            $sqlline = "select itemid, itemname, qty, price, totalorder, discount, totalbayar, qtyedited, totalorderedited, discountedited, totalbayaredited from vw_salesline where qty > 0 and salesid ='" .  $salesid . "'";
                                            $rs3 = $this->db->query($sqlline);
                                            while ( $rs3->fetch() )
                                            {
                                                    $idx ++;
                                                   
                                                    $varLine .= "<tr ". ($idx % 2 == 0 ? "style='background-color:#fbefef'" : "") .">
                                                            <td>".trim($rs3->value('itemid'))."</td>
                                                            <td>".trim($rs3->value('itemname'))."</td>
                                                            <td>".$this->valuenumber(trim($rs3->value('price')))."</td>
                                                            <td>".$this->valuenumber(trim($rs3->value('qty')))."</td>";

                                                    if ( $row['salesstatus'] == '5' ) // edited / revisi
                                                    {
                                                            $varLine .= "<td color='red'>".$this->valuenumber(trim($rs3->value('qtyedited')))."</td>
                                                                         <td style='text-align:right'>".$this->valuenumber($rs3->value('totalorderedited'))."</td>";
                                                            $totalorder += $rs3->value('totalorderedited');
                                                            $totaldiscount += $rs3->value('discountedited');
                                                            $totalbayar += $rs3->value('totalbayaredited');
                                                    }
                                                    else
                                                    {
                                                            $varLine .= "<td style='text-align:right'>".$this->valuenumber($rs3->value('totalorder'))."</td>";
                                                            $totalorder += $rs3->value('totalorder');
                                                            $totaldiscount += $rs3->value('discount');
                                                            $totalbayar += $rs3->value('totalbayar');
                                                    }
                                                    $varLine .=  '</tr>';
                                            }
                                            $rs3->close();
                                            
                                            // Payment instructions
                                            if ($row['salesstatus'] == '6' && strpos($varBody, '[payminstruksi]'))
                                            {
                                                $sql0 = "select paymentmode, paymentname, totalbayar, virtualaccount,trxref,maxpaiddate, getdate() as datenow";
                                                $sql0.= " from vw_paymtable where salesid = " . $this->queryvalue($salesid);
                                                $rs0  = $this->db->query($sql0);
                                                $payminstruksi = "";
                                                if ($rs0->fetch())
                                                {
                                                    $paymmode = $rs0->value('paymentmode');
                                                    $payminstruksi.= "Rp ".$this->valuenumber($rs0->value('totalbayar'));
                                                    if (strcasecmp($paymmode, 'ATM') == 0 && strlen($rs0->value('trxref')) > 0 )
                                                        $payminstruksi .= " melalui virtual account " . $rs0->value('trxref');
                                                    else
                                                        $payminstruksi .= " melalui " . $rs0->value('paymentname');
                                                    $maxpaiddate = strtotime($rs0->value('maxpaiddate'));
                                                    $datenow = strtotime($rs0->value('datenow'));
                                                    if ($maxpaiddate > $datenow)
                                                        $payminstruksi .= " sebelum pkl " . date("g.i a", $maxpaiddate);  
                                                }
                                                $varBody = str_replace('[payminstruksi]', $payminstruksi, $varBody);
                                                //echo $message;
                                            }
                                    }

                                    $varBody = '<span id="wrapper" style="font-family: helvetica, sans-serif, arial; font-size: 12px; color:#727274;">'
                                            . $varBody . '</span>';

                                    if ( $salesid !=  "" )
                                    {
                                            $colspan = ($row['salesstatus'] == '5' ? 6 : 5);
                                            $varBody .= "<hr><table cellspacing='1' cellpadding='1' style='width:100%;'>
                                                    <tbody style='font-family: helvetica, sans-serif, arial; font-size: 12px; color:#727274;'>
                                                            <tr>
                                                                    <td style='background-color:#d0d0d0'>Order</td>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".$salesid."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#d0d0d0'>Tanggal</td>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>" .$this->valuedatetime($row['orderdate']) . "</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#d0d0d0'>Status</td>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($row['userstatus'])."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#d0d0d0' colspan='1' rowspan='3'>Dari member</td>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."'>" .trim($row['namamember']). " (#" . trim($row['kodemember']) . ")</td>
                                                            </tr>
                                                            <tr>
                                                                            <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>Tel: ".trim($row['telp'])."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($row['alamat'])."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#d0d0d0' colspan='1' rowspan='3'>Untuk BC</td>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."'>".trim($row['namabc'])." (#".trim($row['kodebc']).")</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>Tel: <a href='tel:".htmlspecialchars(trim($row['telpbc']))."' value='".str_replace(" ","",trim($row['telpbc']))."' target='_blank'>" . trim($row['telpbc']) . "</a></td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($row['alamatbc'])."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan='".($colspan)."'>&nbsp;</td>
                                                            </tr>
                                                            <tr style='background-color:#d26ca3;color:#ffffff'>
                                                                    <td>Kode</td>
                                                                    <td>Nama barang</td>
                                                                    <td>Harga</td>
                                                                    <td>Jumlah</td>
                                                            ";
                                                            if ( $row['salesstatus'] == '5' ) // edited / revisi
                                                            {
                                                                    $varBody = $varBody . "<td>Tersedia</td>";
                                                            }
                                                            $varBody = $varBody . "<td>Total</td>
                                                            </tr>";

                                                            $varBody = $varBody . $varLine;

                                                            $varBody .= "<tr>
                                                                    <td colspan='".($colspan)."'>&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Total order</td>
                                                                    <td style='text-align:right'>".$this->valuenumber($totalorder)."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Discount member</td>
                                                                    <td style='text-align:right'>".$this->valuenumber($totaldiscount)."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Total setelah discount</td>
                                                                    <td style='text-align:right'>".$this->valuenumber($totalorder+$totaldiscount)."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Biaya admin</td>
                                                                    <td style='text-align:right'>".$this->valuenumber($row['paymentcharge'])."</td>
                                                            </tr>
                                                            <tr>
                                                                    <td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Total pembayaran</td>
                                                                    <td style='background-color:#fbefef;text-align:right'>".$this->valuenumber($totalbayar+$row['paymentcharge'])."</td>
                                                            </tr>
                                                    </tbody>
                                            </table>";
                                    }
                                                                        
                                    if (strlen($paymmode)>0) {
                                        $varBody .= '<hr/><br/><span id="wrapper" style="font-family: helvetica, sans-serif, arial; font-size: 12px; color:#727274;">';
                                        $varBody .= '<p><strong>Payment instructions:</strong></p>';
                                        $varBody .= file_get_contents('../include/paymode_' . strtolower($paymmode) . '.php');
                                        $varBody .= '</span>';  
                                    }

                                    $mail->Subject = $row['subject'];
                                    $mail->MsgHTML($varBody);

                                    $mail->Send();

                                    $sqlUpdate = "update emailtable set sendDate=getdate() where noseq =" . $row['noseq'];
                                    $this->db->execute($sqlUpdate);
                                    
                                    if (strlen($salesid) > 0)
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][sendemail][".$row['salesid']."][" . trim($row['userstatus']). "] Email sent\n";
                                    else
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][sendemail][to:" . trim($row['to']). "][" . $row['subject'] . "] Email sent\n";
                            } 
                            catch (Exception $e) 
                            {
                                    $sqlUpdate = "update emailtable set retrynumber = isnull(retrynumber,0) + 1 where noseq =" . $row['noseq'];
                                    $this->db->execute($sqlUpdate);
                                    
                                    if (strlen($salesid) > 0)
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][sendemail][".$row['salesid']."] Email not sent! Exception ". $e->getMessage() ."\n";
                                    else
                                        echo "[BATCH][".date("Y-m-d H:i:s")."][sendemail][to:" . trim($row['to']). "][" . $row['subject'] . "] Email not sent! Exception ". $e->getMessage() ."\n"; 
                            }
                        }
                    }

		}
		
		function sendingsms()
		{
                    $sqlUpdate = "";
                    $status = -1;
                    $msgid = "";
                    $salesid = "";
                    $phonenumber = "";
                    $smsurl = $this->sysparam['dbsms']['url'];

                    $sql = "select [noseq], isnull(phone,'') as phone, Replace(message,' ','%20') as message,isnull(salesid,'') as salesid";
                    $sql.= " from smsTable with (NOLOCK) where sendDate is null and isnull(retrynumber,0) < 3";
                    $rs	= $this->db->query($sql);
                    
                    if ($rs)
                    {
                        $rows = $rs->fetchAll();
                        $rs->close();
                        
                        foreach ($rows as $row) 
                        {
                            try 
                            {
                                    $phonenumber = $row['phone'];
                                    $salesid = $row['salesid'];
                                    $message = $row['message'];
                                    if ( $phonenumber != '' )
                                    {
                                            if ( substr($phonenumber,0,1) == '0' )
                                                    $phonenumber = '62' . substr($phonenumber,1,strlen($phonenumber)-1);
                                            if ( substr($phonenumber,0,2) != '62' )
                                                    $phonenumber = '62' . $phonenumber;
                                            
                                            if (strpos($message, '[payminstruksi]'))
                                            {
                                                $sql0 = "select paymentmode, paymentname, totalbayar, virtualaccount,trxref, maxpaiddate, getdate() as datenow";
                                                $sql0.= " from vw_paymtable where salesid = " . $this->queryvalue($salesid);
                                                $rs0  = $this->db->query($sql0);
                                                $payminstruksi = "";
                                                if ($rs0->fetch())
                                                {
                                                    $payminstruksi.= "Rp ".$this->valuenumber($rs0->value('totalbayar'));
                                                    if (strcasecmp($rs0->value('paymentmode'), 'ATM') == 0 && strlen($rs0->value('trxref')) > 0 )
                                                        $payminstruksi .= " ke rek " . $rs0->value('trxref');
                                                    else
                                                        $payminstruksi .= " melalui " . $rs0->value('paymentname');
                                                    $maxpaiddate = strtotime($rs0->value('maxpaiddate'));
                                                    $datenow = strtotime($rs0->value('datenow'));
                                                    if ($maxpaiddate > $datenow)
                                                        $payminstruksi .= " sebelum pkl " . date("g.i a", $maxpaiddate);  
                                                }
                                                $message = str_replace('[payminstruksi]', urlencode($payminstruksi), $message);
                                                //echo $message;
                                            }

                                            $smsurlsent = $smsurl . '&message=' . $message . '&msisdn=' . $phonenumber;
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
                                                    $sqlUpdate = "update smsTable set sendDate=getdate(), messageid='" . $msgid . "' where noseq =" . $row['noseq'];
                                                    $this->db->execute($sqlUpdate);
                                                    echo "[BATCH][".date("Y-m-d H:i:s")."][sendsms][salesid:".$salesid."][".$phonenumber. "] SMS sent\n";
                                            }
                                            else
                                            {
                                                    $sqlUpdate = "update smsTable set retrynumber = isnull(retrynumber,0) + 1 where noseq =" . $row['noseq'];
                                                    $this->db->execute($sqlUpdate);
                                                    echo "[BATCH][".date("Y-m-d H:i:s")."][sendsms][salesid:".$salesid."][".$phonenumber. "] SMS not sent! Jatis returned status $status\n";
                                            }
                                            
                                    }                                  
                            } 
                            catch (Exception $e) 
                            {
                                    $sqlUpdate = "update smsTable set retrynumber = isnull(retrynumber,0) + 1 where noseq =" . $row['noseq'];
                                    $this->db->execute($sqlUpdate);
                                    echo "[BATCH][".date("Y-m-d H:i:s")."][sendsms][salesid:".$salesid."][".$phonenumber."] SMS not sent! Exception ". $e->getMessage() ."\n";
                            }
                        }
                    }
		}
		
	}
	
?>