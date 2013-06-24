<?php
	include_once "library/database.php";
	include_once "library/emailsmscontroller.php";
	include_once "library/class.phpmailer.php";
	include_once "library/syscontroller.php";
 
	$sqlUpdate = "";
	$varBody = "";
	$varLine = "";
	$salesid = "";
	$idx = 0;
	$totalorder = 0;
	$totaldiscount = 0;
	$totalbayar = 0;
	$colspan = 0;
	
	$execute = new emailsmscontroller;
	$control = new syscontroller;
	
	$query = new emailsmscontroller;
	$sql = "select top 30 [noseq],[from],[to],isnull([cc],'') as cc,isnull([bcc],'') as bcc,[subject],[body],t1.[createdDate],[sendDate],isnull([toname],[to]) as toname, t1.[salesid] as salesid, t2.status as [salesstatus] ";
	$sql.= " ,t2.orderdate, t2.kodemember, t2.namamember, t2.telp, isnull(t2.alamat,'') as alamat, t2.kodebc, t2.namabc, t2.alamatbc, t2.telpbc, t2.userstatus, t2.bcsalesorderstatus, t2.totalorder, t2.discount, t2.totalbayar, isnull(t2.paymentcharge,0) as paymentcharge ";
	$sql.= " from emailTable as t1 with (NOLOCK) left join vw_salestable as t2 with (NOLOCK) on t1.salesid=t2.salesid";
	$sql.= " where T1.sendDate is null";

	$rs	= $query->db->query($sql);
	while ( $rs->fetch() )
	{
		try 
		{
			$mail = new PHPMailer(true); 	// the true param means it will throw exceptions on errors, which we need to catch
			$mail->IsSMTP(); 				// telling the class to use SMTP

			// mail property
			$mail->Host       = "10.0.0.17"; 				// SMTP server
			//$mail->SMTPAuth   = true;                 	// enable SMTP authentication
			$mail->Port       = 25;                    		// set the SMTP port for the GMAIL server
			$mail->Username   = "victor@sophieparis.com"; 	// SMTP account username

			// Mail Address
			$mail->SetFrom('victor@sophieparis.com', 'Sophie Online Orders (NO REPLY)');
			$mail->AddAddress( trim($rs->value('to')), trim($rs->value('toname')) );
			if ( trim($rs->value('cc')) != '' )
				$mail->AddCC(trim($rs->value('cc')));

			if ( trim($rs->value('bcc')) != '' )
				$mail->AddBCC(trim($rs->value('bcc')));	

			// Mail Message
			// List item
			
			$salesid = trim($rs->value('salesid'));
			if ( $salesid !=  "" )
			{
				$idx = 0;
				$totalorder = 0;
				$totaldiscount = 0;
				$totalbayar = 0;
                                $varLine = "";
				$sqlline = "select itemid, itemname, qty, price, totalorder, discount, totalbayar, qtyedited, totalorderedited, discountedited, totalbayaredited from vw_salesline where salesid ='" .  $salesid . "'";
				$rs3 = $query->db->query($sqlline);
				while ( $rs3->fetch() )
				{
					$idx ++;
					$varLine .= "<tr ". ($idx % 2 == 0 ? "style='background-color:#fbefef'" : "") .">
						<td>".trim($rs3->value('itemid'))."</td>
						<td>".trim($rs3->value('itemname'))."</td>
						<td>".$control->valuenumber(trim($rs3->value('price')))."</td>
						<td>".$control->valuenumber(trim($rs3->value('qty')))."</td>";
						
					if ( $rs->value('salesstatus') == '5' ) // edited / revisi
					{
						$varLine .= "<td>".$control->valuenumber(trim($rs3->value('qtyedited')))."</td>
									<td style='text-align:right'>".$control->valuenumber($rs3->value('totalorderedited'))."</td>";
						$totalorder += $rs3->value('totalorderedited');
						$totaldiscount += $rs3->value('discountedited');
						$totalbayar += $rs3->value('totalbayaredited');
					}
					else
					{
						$varLine .= "<td style='text-align:right'>".$control->valuenumber($rs3->value('totalorder'))."</td>";
						$totalorder += $rs3->value('totalorder');
						$totaldiscount += $rs3->value('discount');
						$totalbayar += $rs3->value('totalbayar');
					}
					$varLine .=  '</tr>';
				}
				$rs3->close();
			}
			
			$colspan = ($rs->value('salesstatus') == '5' ? 6 : 5);
			
			$varBody =
				'<body style="font-family: helvetica, sans-serif, arial;	font-size: 15px; margin: 0;	padding: 0;	color:#727274;">'
				. $rs->value('body') . 
				"<br><br>";
			if ( $salesid !=  "" )
			{
				$varBody .= "<table cellspacing='1' cellpadding='1' style='width:100%;border:0'>
					<tbody>
						<tr>
							<td style='background-color:#d0d0d0'>Order</td>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".$salesid."</td>
						</tr>
						<tr>
							<td style='background-color:#d0d0d0'>Tanggal</td>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>" .$control->valuedatetime($rs->value('orderdate')) . "</td>
						</tr>
						<tr>
							<td style='background-color:#d0d0d0'>Status</td>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($rs->value('userstatus'))."</td>
						</tr>
						<tr>
							<td style='background-color:#d0d0d0' colspan='1' rowspan='3'>Dari member</td>
							<td style='background-color:#efefef' colspan='".($colspan-1)."'>" .trim($rs->value('namamember')). " (#" . trim($rs->value('kodemember')) . ")</td>
						</tr>
						<tr>
								<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>Tel: ".trim($rs->value('telp'))."</td>
						</tr>
						<tr>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($rs->value('alamat'))."</td>
						</tr>
						<tr>
							<td style='background-color:#d0d0d0' colspan='1' rowspan='3'>Untuk BC</td>
							<td style='background-color:#efefef' colspan='".($colspan-1)."'>".trim($rs->value('namabc'))." (#".trim($rs->value('kodebc')).")</td>
						</tr>
						<tr>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>Tel: <a href='tel:".htmlspecialchars(trim($rs->value('telpbc')))."' value='".str_replace(" ","",trim($rs->value('telpbc')))."' target='_blank'>" . trim($rs->value('telpbc')) . "</a></td>
						</tr>
						<tr>
							<td style='background-color:#efefef' colspan='".($colspan-1)."' rowspan='1'>".trim($rs->value('alamatbc'))."</td>
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
						if ( $rs->value('salesstatus') == '5' ) // edited / revisi
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
							<td style='text-align:right'>".$control->valuenumber($totalorder)."</td>
						</tr>
						<tr>
							<td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Discount member</td>
							<td style='text-align:right'>".$control->valuenumber($totaldiscount*-1)."</td>
						</tr>
						<tr>
							<td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Total setelah discount</td>
							<td style='text-align:right'>".$control->valuenumber($totalorder-$totaldiscount)."</td>
						</tr>
						<tr>
							<td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Ongkos pembayaran</td>
							<td style='text-align:right'>".$control->valuenumber($rs->value('paymentcharge'))."</td>
						</tr>
						<tr>
							<td style='text-align:right' colspan='".($colspan-1)."' rowspan='1'>Total pembayaran</td>
							<td style='background-color:#fbefef;text-align:right'>".$control->valuenumber($totalbayar+$rs->value('paymentcharge'))."</td>
						</tr>
					</tbody>
				</table>";
			}
			
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