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

<? if ($ctrl->paymentmode != "CC") { ?>
<div class="boxfont1">Sophie Online Shopping</div>
<table class="dataview" width="340">
	<tr>
		<th>Order #</th>
		<th>Date Time</th>
		<th>Total</th>
		<th>Status</th>
	</tr>
	<tr>
		<td><?=$ctrl->varvalue('salesid')?></td>
		<td><?=$ctrl->varvalue('orderdate')?></td>
		<td><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></td>
		<td><?=$ctrl->varvalue('status')?></td>
	</tr>
</table>
<br>
<? } ?>

<? if ($ctrl->paymentmode == "ATM") { ?>
<div class="errmsg"style="text-align:left;padding-left:9px">Tinggal <?=$ctrl->varvalue('timeleft')?> untuk melakukan pembayaran online.</div>
<br>
<div class="boxfont2">Silahkan transfer <b><font class="pink">IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></font></b> ke virtual account:</div>
<div class="boxfont1"><?=$ctrl->varvalue('virtualaccount')?></div>
<div class="boxfont2">
Proses pembayaran di ATM:<br>
- ATM Bersama, Alto & Prima Only<br>
- Enter ATM Pin<br>
- Select Transfer Menu<br>
- Select Transfer to Other Bank<br>
- Select Permata Bank(13)<br>
- Enter Virtual Account<br>
</div>
<p><a href="http://webdev.sophiemartin.com/sos/mbrpaymentconfirm.php?pageaction=simatm&vanumber=<?=$ctrl->varvalue('virtualaccount')?>">Payment init</a></p>
<? } ?>


<? if ($ctrl->paymentmode == "SMSBRI") { ?>
<div class="errmsg"style="text-align:left;padding-left:9px">Tinggal <?=$ctrl->varvalue('timeleft')?> untuk melakukan pembayaran online.</div>
<br>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>
<? } ?>

<? if ($ctrl->paymentmode == "SMSMANDIRI") { ?>
<div class="errmsg"style="text-align:left;padding-left:9px">Tinggal <?=$ctrl->varvalue('timeleft')?> untuk melakukan pembayaran online.</div>
<br>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont2">
Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:<br>
BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]
</div>
<? } ?>



<?include "mbrfooter.php";?>