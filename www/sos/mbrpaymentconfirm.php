<?include "mbrheader.php";?>
<? if ($ctrl->paymentmode == "CC") { ?>
Redirecting to credit card payment gateway<br>
Please wait....<br>
<input type="hidden" id="MERCHANTID" name="MERCHANTID" value="<?=$ctrl->merchantid?>">
<input type="hidden" id="sTxnPassword" name="sTxnPassword" value="<?=$ctrl->password?>">
<input type="hidden" id="MERCHANT_TRANID" name="MERCHANT_TRANID" value="<?=$ctrl->salesid?>">
<input type="hidden" id="AMOUNT" name="AMOUNT" value="<?=$ctrl->totalbayar?>">
<input type="hidden" id="SIGNATURE" name="SIGNATURE" value="<?=strtoupper(sha1("##".strtoupper($ctrl->merchantid)."##".strtoupper($ctrl->password)."##".strtoupper($ctrl->salesid)."##".$ctrl->totalbayar."##0##"))?>">
<? } ?>
<br>
<div style="color:red">Tinggal <?=$ctrl->varvalue('timeleft')?> untuk melakukan pembayaran online.</div>
<br>
<? if ($ctrl->paymentmode != "CC") { ?>
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
<? } ?>

<? if ($ctrl->paymentmode == "ATM") { ?>

<br>
<div class="boxfont2">Silahkan transfer <b><font class="pink">IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></font></b> ke virtual account:</div>
<div class="boxfont1"><?=$ctrl->varvalue('virtualaccount')?></div>
<div class="boxcon5">Instruksi pembayaran</div>
<div class="boxfont3">
	<ul>
		<li>ATM Bersama, Alto & Prima Only</li>
		<li>Enter ATM PIN</li>
		<li>Select Transfer Menu</li>
		<li>Select Transfer To Other Bank</li>
		<li>Select Permata Bank (13)</li>
		<li>Enter Virtual Account</li>
	</ul>
</div>
<p><a href="mbrpaymentconfirm.php?pageaction=simatm&salesid=<?=$ctrl->varvalue('salesid')?>&vanumber=<?=$ctrl->varvalue('virtualaccount')?>">Payment init</a></p>
<? } ?>


<? if ($ctrl->paymentmode == "SMSBRI") { ?>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>
<? } ?>

<? if ($ctrl->paymentmode == "SMSMANDIRI") { ?>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>
<? } ?>



<?include "mbrfooter.php";?>
