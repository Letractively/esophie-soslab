<?include "mbrheader.php";?>
<h3>Terima kasih atas pesanan Anda!</h3>
<table>
	<tr>
		<td align="right"><b>Order#</b></td>
		<td><?=$ctrl->varvalue('salesid')?></td>
	</tr>
	<tr>
		<td align="right"><b>BC</b></td>
		<td><?=$ctrl->varvalue('bcno')?> - <?=$ctrl->varvalue('bcname')?></td>
	</tr>
	<tr>
		<td align="right"><b>Total</b></td>
		<td><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></td>
	</tr>
</table>
<br>
<p>Silahkan tunggu 30-60 menit untuk mendapatkan konfirmasi pesanan anda dari BC.</p>
<?include "mbrfooter.php";?>