<?include "mbrheader.php";?>
<input type="hidden" id="edit" name="edit" value="<?=$ctrl->value("edit")?>">
	<?if ($ctrl->pageview != 'orderedit' && $ctrl->pageview != 'orderconfirm') { ?>
	<div class="boxcon3">
		<div class="boxleft3">
			<?if ($ctrl->mbrmsg['color'] != '') {?>
			<div class="color<?=$ctrl->mbrmsg['color']?>"></div>
			<?}?>
		</div>
		<div class="boxright3"><b><?=$ctrl->mbrmsg['title']?></b><br><?=$ctrl->mbrmsg['body']?></div>
		<? if (isset($ctrl->mbrmsg['link1'])) { ?>
		<div class="boxcon3-1">
			<?if (isset($ctrl->mbrmsg['link1'])) {?><a href="<?=$ctrl->mbrmsg['link1']?>"><?=$ctrl->mbrmsg['link1label']?></a><?}?>
			<?if (isset($ctrl->mbrmsg['link2'])) {?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$ctrl->mbrmsg['link2']?>"><?=$ctrl->mbrmsg['link2label']?></a><?}?>
		</div>
		<? } ?>
	</div>
	<? } ?>

<? 
	switch ($ctrl->pageview) 
	{ 
		case "orderedit" : ?>
			<div class="boxcon5">1 - Silahkan pilih BC yang akan proses order Anda</div>
			<div class="boxcon">
				<div class="boxleft" style="width:150px">
					<div class="boxstyled2"><select name="bc" id="bc" onchange="setaction('refreshbc')"><? $ctrl->getbc(); ?></select></div>
					<input type="checkbox" name="defaultbc" id="defaultbc" value="1" <?if ($ctrl->value('defaultbc') == '1') echo 'checked';?>> set sebagai <font class="pink">default BC</font>.
				</div>
				<div class="boxright" style="width:150px">
					<?=$ctrl->varvalue('bcaddress')?>
					<br><?=$ctrl->varvalue('bcphone')?>
				</div>
			</div>
			<div class="boxcon5">2 - Silahkan cek kembali order Anda</div>			
<?			break;

		case "orderconfirm" : ?>
			<div class="boxcon4" style="text-align:left">
				Proses order hampir selesai! setelah Anda klik "Kirim ke BC", order anda akan di proses oleh BC# <?=$ctrl->bcno?> dalam waktu 1 jam.
				Anda dapat melanjutkan ke pembayaran setelah Anda meneriama konfirmasi dar BC.
			</div>
			<div class="boxcon5">Silahkan update data pribadi Anda</div>
			<div style="text-align:left;">
				Konfirmasi order yang sudah di validasi oleh BC atau pembayaran yang sudah diterima akan dikirimkan melalui SMS dan email.
			</div>
			<?=$ctrl->printerrors();?>
			<div class="boxstyled1"><div>Handphone (ex: 081234567890)</div><input type="textbox" name="handphone" id="handphone" maxlength="50" placeholder="Handphone Number" value="<?=$ctrl->mbrphone?>"></div>
			<div class="boxstyled1"><div>Email (optional)</div><input type="textbox" name="email" id="email" maxlength="80" placeholder="Email Address" value="<?=$ctrl->mbremail?>"></div>	
<?			break;

		case "confirmqtychange" : 
			if ( $ctrl->varvalue('isanyitemsold') <= 0 ) 
			{ ?>
				<div class="boxfont2"style="color:#ff0000">Maaf, order anda tidak dapat dipenuhi</div>
<?			} 
			break;
	}
	?>	
	
	<?if ($ctrl->pageview != 'orderedit') { ?>
	<div class="boxcon">
		<div class="boxleft" style="width:150px">
			<div class="boxcon5" style="text-align:left">Dari member</div>
			<b>Member #<?=$ctrl->varvalue('mbrno')?></b>
			<br><?=$ctrl->varvalue('mbrname')?>
			<br><?=$ctrl->varvalue('mbraddress')?>
		</div>
		<div class="boxright" style="width:160px">
			<div class="boxcon5" style="text-align:left">Untuk BC</div>
			<b><?=$ctrl->varvalue('bcname')?>&nbsp(<?=$ctrl->varvalue('bcno')?>)</b>
			<br><?=$ctrl->varvalue('bcaddress')?>
		</div>
	</div>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th>Order #</th>
				<th>Date Time</th>
				<th>Total</th>
				<th>Status</th>
			</tr>
			<tr>
				<td><?=$ctrl->varvalue('salesid')?></td>
				<?if ($ctrl->pageview == 'orderconfirm') { ?>
				<td><?=$ctrl->varvalue('createddate')?></td>
				<? } else { ?>
				<td><?=$ctrl->varvalue('orderdate')?></td>
				<? } ?>
				<td><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></td>
				<td><?=$ctrl->varvalue('status')?></td>
				
			</tr>
		</table>
	</div>		
	<? } ?>
	
	<?if ($ctrl->pageview == 'orderedit') echo $ctrl->printerrors();?>
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
				$i=0;
				if (is_array($ctrl->items))
				{					
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
					}
				}
				else
				{
					if ($ctrl->pageview != 'orderedit') echo '<td colspan="5" align="center">no items</td>';
				}
				
				if ($ctrl->pageview == 'orderedit')
				{
					echo $i++%2?'<tr class="pinkrow"':'<tr';
					echo ' style="cursor:pointer" onclick="setaction(\'tambah\');">';
					echo '<td colspan="5" align="center">Tambah item</td>';
					echo '</tr>';		
				}
			?>
		</table>
	</div>
	
	<?if ($ctrl->pageview == 'orderedit') { ?>
	<div class="boxcon" style="text-align:right;">
		<button type="button" onclick="setaction('refresh');">Refresh</button>
	</div>
	<? } ?>
	<div class="boxcon1">
		<div class="boxleft1">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Setelah Diskon</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder')+$ctrl->varvalue('discount'));?></div>
	</div>
	<?if ($ctrl->varvalue('paymentmode') != '') { ?>
	<div class="boxcon1">
		<div class="boxleft1">Payment Charge (<?=$ctrl->varvalue('paymentname')?>)</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('paymentcharge'));?></div>
	</div>
	<? } ?>
	<div class="boxcon1">
		<div class="boxleft1">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
	</div>
<? 
	switch ($ctrl->pageview) 
	{ 
		case "orderedit" : ?>
			<button type="button" onclick="setaction('neworder');">Order Baru</button>
			<button type="button" onclick="setaction('confirmorder');">Konfirmasi</button>
<?		break;

		case "orderconfirm" : ?>
			<button type="button" onclick="setaction('back');" style="width:80px">Kembali</button>
			<!--<button type="button" onclick="setaction('batalorder');">Batal Order</button>-->
			<button type="button" onclick="setaction('sendordertobc');">Kirim ke BC</button>
<?		break;

		case "confirmqtychange" : ?>
			<button type="button" onclick="setaction('cancel');">Batal</button>
<? 		if ( $ctrl->varvalue('isanyitemsold') > 0 ) { ?>
				<button type="button" onclick="setaction('confirmqtychange');">Konfirm dan bayar</button>
<? 		} ?>
<?		break;

		case "pembayaran" : ?>
			<div class="errmsg"style="text-align:center;padding-left:9px">Waktu anda tinggal <? echo $ctrl->varvalue("timeleft"); ?> untuk melakukan pembayaran online</div><br>
			<button type="button" onclick="setaction('pembayaran');">Bayar</button>
<?		break; 		
	}
?>	
<?include "mbrfooter.php";?>