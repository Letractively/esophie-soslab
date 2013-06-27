<?include "mbrheader.php";?>
<br>
<div style="color:red">Tinggal <?=$ctrl->varvalue('timeleft')?> untuk melakukan pembayaran online.</div>
<br>
<table class="dataview" width="340">
	<tr>
		<th width="8">&nbsp;</th>
		<th>Order #</th>
		<th>Date Time</th>
		<th>Total</th>
		<th>Status</th>
	</tr>
	<tr>
		<td><div class="color<?=$ctrl->colorstatus($ctrl->varvalue('status'))?>"></div></td>
		<td><?=$ctrl->varvalue('salesid')?></td>
		<td><?=$ctrl->varvalue('orderdate')?></td>
		<td><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></td>
		<td><?=$ctrl->varvalue('userstatus')?></td>
	</tr>
</table>
<br>

<? if ($ctrl->paymentmode == "ATM") { ?>

<br>
<div class="boxfont2">Silahkan transfer <b><font class="pink">IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></font></b> ke virtual account:</div>
<div class="boxfont1"><?=$ctrl->varvalue('virtualaccount')?></div>

<? } else if ($ctrl->paymentmode == "SMSBRI") { ?>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>

<? } else if ($ctrl->paymentmode == "SMSMANDIRI") { ?>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>
<? } ?>

<button type="button" onclick="setaction('simulate');" class="buttonbig" >Simulate</button>
<button type="button" onclick="seturl('<?=$ctrl->varvalue('urlforward')?>');" class="buttonbig" >Lanjut >></button>


<?include "mbrfooter.php";?>