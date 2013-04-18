<?include "mbrheader.php";?>
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
			
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th width="30">Kode</th>
				<th width="230">Nama Barang</th>
				<th width="50" align="right">Harga</th>
				<th width="30" align="right">Jumlah</th>
				<th width="70" align="right">Total</th>
			</tr>
			<?=$ctrl->printitems(true);?>
		</table>
	</div>
	<div class="boxcon" style="text-align:right;">
		<button type="button" onclick="setaction('refresh');">Refresh</button>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Setelah Diskon</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxright"><i>Total order belum termasuk ongkos pembayaran online</i></div>
	</div>
	<br>
	<?if ( $ctrl->errormsg != "" ) echo '<div class="boxerr1">'.$ctrl->errormsg.'</div>'; ?>
	<button type="button" onclick="setaction('confirmorder');">Konfirmasi</button>
	<?=$ctrl->printerrors();?>
<?include "mbrfooter.php";?>