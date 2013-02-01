<?include "bcheaderright.php";?>
<div class="boxcon" style="width:510px;padding-left:100px">
	<div class="title">Order Edited</div>
	<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">
	<table class="dataview" width="500px">
		<tr>
			<th align="left">Order #</th>
			<th align="left">Date Time</th>
			<th align="right">Total</th>
			<th align="left">Status</th>
		</tr>
		<tr>
			<td align="left"><?=$ctrl->value('salesid')?></td>
			<td align="left"><?=$ctrl->varvalue('orderdate')?></td>
			<td align="right"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'))?></td>
			<td align="left"><?=$ctrl->varvalue('status')?></td>
		</tr>
	</table>
	<br>

	<table class="dataview" width="500px">
		<tr>
			<th width="40" align="left">Kode</th>
			<th width="250" align="left">Nama Barang</th>
			<th width="60" align="right">Harga</th>
			<th width="40" align="right">Jumlah</th>
			<th width="60" align="right">Total</th>
		</tr>
		<?
			if (is_array($ctrl->items))
			{
				foreach ($ctrl->items as $item)
				{
					echo "<tr>";
					echo "<td align=\"left\">" . $item['itemid'] . "</td>";
					echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['qty']) . "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalorder']) . "</td>";
					echo "</tr>";
				}
			}
			else
			{
				echo '<td colspan="5" align="center">no items</td>';
			}
		?>
	</table>
	<br>
	<div class="boxcon" style="width:495px">
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Total Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Total Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Payment Charge (<?=$ctrl->varvalue("paymentname")?>)</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('paymentcharge'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
	</div>
	<br>
	<div style="width:495px;text-align:right">
		<div class="errmsg" style="text-align:left">Order ini akan dikonfirmasi lagi oleh member, email dan sms akan dikirim ke member setelah validasi order selesai.</div>
		<br>
		<button type="button" onclick="setaction('ok');" style="width:80px;">OK</button>
	</div>
</div>
<?include "bcfooterright.php";?>