<?include "mbrheader.php";?>
	<? 
		switch ($ctrl->pageview) 
		{ 
			case "orderedit" : echo "<h2>Check Order</h2>"; break;
			case "orderconfirm" : echo "<h2>Konfirmasi Order</h2>"; break;
			case "confirmqtychange" : echo "<h2>Konfirmasi Order</h2>"; 
				if ( $ctrl->varvalue('isanyitemsold') <= 0 ) {		?>
					<div class="boxfont2"style="color:#ff0000">Maaf, order anda tidak dapat dipenuhi</div>
	<?			} break;
			case "pembayaran" : echo "<h2>Pembayaran</h2>"; break; 
			case "waiting" : ?>
			<div class="boxfont2"style="color:#ff0000">
			<? if ( $ctrl->varvalue('validatesameday') == 0 ) 
				echo "Order anda lagi diproses oleh BC.<br> Silahkan tunggu 30-60 menit untuk mendapatkan konfimasi pesanan anda dari BC.";
			else
				echo "Order anda akan diproses oleh BC di hari kerja berikutnya dikarenakan pemesanan anda diluar jam kerja."; ?>
			</div>
			
<?			break;
		}
	?>
	<input type="hidden" id="edit" name="edit" value="<?=$ctrl->value("edit")?>">
	<div class="boxcon">
		<div class="boxleft" style="width:150px">
			<b>Member #<?=$ctrl->varvalue('mbrno')?></b>
			<br><?=$ctrl->varvalue('mbrname')?>
			<br><?=$ctrl->varvalue('mbraddress')?>
		</div>
		<div class="boxright" style="width:150px">
			<b>BC #<?=$ctrl->varvalue('bcno')?></b>
			<br><?=$ctrl->varvalue('bcname')?>
			<br><?=$ctrl->varvalue('bcaddress')?>
		</div>
	</div>
	<?if ($ctrl->pageview != "orderedit" && $ctrl->pageview != "orderconfirm") { ?>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th>Order #</th>
				<th>Date Time</th>
				<th>Status</th>
				<!-- <th>Sisa Waktu</th> -->
			</tr>
			<tr>
				<td><?=$ctrl->varvalue('salesid')?></td>
				<td><?=$ctrl->varvalue('orderdate')?></td>
				<td><?=$ctrl->varvalue('status')?></td>
				
			</tr>
		</table>
	</div>
	<? } ?>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th width="30">Kode</th>
				<th width="230">Nama Barang</th>
				<th width="50" align="right">Harga</th>
				<th width="30" align="right">Jumlah</th>
				<?if ($ctrl->pageview == 'confirmqtychange') { ?> <th width="30" align="right">Tersedia</th> <?}?>
				<th width="70" align="right">Total</th>
			</tr>
			<?
				if (is_array($ctrl->items))
				{
					$i=0;
					foreach ($ctrl->items as $item)
					{					
						echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
						echo "<td align=\"left\">" . $item['itemid'] . "</td>";
						echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
						echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";
						if ($ctrl->pageview == 'orderedit')
						{	
							?>
							<td>
								<input type="hidden" name="itemid[]" id="itemid[]" value="<?=$item['itemid']?>">
								<input type="textbox" name="itemqty[]" id="itemqty[]" value="<?=$item['qty']?>" maxlength="3" size="3" style="text-align:right">
							</td>
							<?
						}
						else
						{
							echo "<td align=\"right\">" . $ctrl->valuenumber($item['qty']) . "</td>";
						}	
						if ($ctrl->pageview == 'confirmqtychange') { echo "<td align=\"right\">" . $ctrl->valuenumber($item['qtyavail']) . "</td>"; }
						echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalorder']) . "</td>";
						echo "</tr>";
						
						if ($ctrl->value("item".($i-1)."err") != '') { 
							echo '<tr><td colspan="5"><div class="boxerr1">' . $ctrl->value("item".($i-1)."err") . '</div></td></tr>';
						}
					}
				}
				else
				{
					echo '<td colspan="5" align="center">no items</td>';
				}
			?>
		</table>
	</div>
	<?if ($ctrl->pageview == 'orderedit') { ?>
	<div class="boxcon" style="text-align:right;">
		<button type="button" onclick="setaction('tambah');">Tambah Items</button>&nbsp;
		<button type="button" onclick="setaction('refresh');">Refresh</button>
	</div>
	<? } ?>
	<div class="boxcon1">
		<div class="boxleft1">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
	</div>
	
	<?if ($ctrl->varvalue('paymentmode') != '') { ?>
	<div class="boxcon1">
		<div class="boxleft1">Payment Charge (<?=$ctrl->varvalue('paymentname')?>)</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('paymentcharge'));?></div>
	</div>
	<? } ?>
	<div class="boxcon1">
		<div class="boxleft1">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
	</div>
	<br><br>
	<?
		if ( $ctrl->value("mbrvieworder_error") != '' )
		{
			echo '<div class="boxerr1">' . $ctrl->value("mbrvieworder_error") . '</div>';
		}
	?>
	<? 
		switch ($ctrl->pageview) 
		{ 
			case "orderedit" : ?>
				<button type="button" onclick="setaction('confirmorder');">Konfirmasi</button>
	<?		break;
			case "orderconfirm" : ?>
				<button type="button" onclick="setaction('sendordertobc');">Kirim Order ke BC</button>
	<?		break;
			case "confirmqtychange" : ?>
				<button type="button" onclick="setaction('cancel');">Pesanan Dibatalkan</button>
	<? 		if ( $ctrl->varvalue('isanyitemsold') > 0 ) { ?>
					<button type="button" onclick="setaction('confirmqtychange');">Konfirmasi</button>
	<? 		} ?>
	<?		break;
			case "pembayaran" :
	?>
				<div class="errmsg"style="text-align:center;padding-left:9px">Waktu anda tinggal <? echo $ctrl->varvalue("timeleft"); ?> untuk melakukan pembayaran online</div><br>
				<button type="button" onclick="setaction('pembayaran');">Pembayaran</button>
	<?		break; 		
		}
	?>	
<?include "mbrfooter.php";?>