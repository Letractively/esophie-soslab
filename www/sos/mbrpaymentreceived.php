<?include "mbrheader.php";?>

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
	
<div class="boxfont2">Terima kasih atas pembayaran anda, transaksi ini sedang divalidasi oleh Sophie Paris Indonsia. Setelah validasi maka anda akan menerima email dari Sophie Paris Indonesia. Order anda siap diambil di BC pilih dalam 2-3 hari kerja.</div>

<?include "mbrfooter.php";?>