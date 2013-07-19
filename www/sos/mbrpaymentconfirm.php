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

<? if (strlen($ctrl->trxref)>0) { ?>
<div class="boxfont2">Silahkan tunggu 2-3 menit untuk initialisasi pembayaran...</div>

<? } else if ($ctrl->paymentmode == "ATM") { ?>
<br>
<div class="boxfont2">Silahkan transfer <b><font class="pink">IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></font></b> ke virtual account:</div>
<div class="boxfont1"><?=$ctrl->varvalue('virtualaccount')?></div>

<? } else if ($ctrl->paymentmode == "SMSBRI") { ?>
<br>
<div class="boxfont2">Untuk pembayaran melalui <b><?=$ctrl->paymentname?></b></div>
<div class="boxfont1">Kirim SMS ke <b><?=$ctrl->paymentto?></b> dengan format:</div>
<div class="boxfont1">BAYAR <?=$ctrl->merchantid?> <?=$ctrl->salesid?> <?=$ctrl->totalbayar?> [PIN]</div>
<? } ?>

<br><div class="boxcon3" style="text-align:left;padding: 10px 10px 10px 10px;">
    <img style="float:right;" src="images/logo-payment-<?= strtolower($ctrl->paymentmode)?>.png"/>
    Instruksi pembayaran <?=$ctrl->paymentname?>:<br><br><? include 'include/paymode_' . strtolower($ctrl->paymentmode) . '.php'; ?>
</div>

<? 
if (strlen($ctrl->trxref)>0)
{
    if ($ctrl->paymentmode == "ATM" || $ctrl->paymentmode == "SMSBRI") { ?>
    <div class="boxfont2">Untuk informasi pembayaran yang lebih lengkap, silahkan clik di button <em>Lanjut >></em> bahwa ini.</div>
    <? } else { ?>
    <div class="boxfont2">Setelah clik button <em>Lanjut >></em>, Anda akan diredireksi ke checkout Faspay untuk melakukan online payment...</div>
<br>
<? } ?>
<table border="0" width="100%">
    <tr>
    <td><input type="button" class="buttonback" onclick="setaction('back')" value="&lt;&lt; Kembali"/></td>
    <td align="right"><?
        if (strlen($ctrl->trxref)>0) { ?>
        <input type="button" class="buttongo" onclick="setaction('forward')" value="Lanjut &gt;&gt;" />
        <? } else { ?>&nbsp;<? } ?>
    </td></tr>
</table>

    


<?include "mbrfooter.php";?>