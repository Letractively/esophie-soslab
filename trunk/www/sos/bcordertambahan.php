<?include "bcheaderright.php";?>
<div class="boxcon" style="width:510px;padding-left:100px">
	<div class="title">Order Tambahan BC Kepada Sophie Martin Indonesia</div>
	<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">
	<div class="boxcon">
		<div class="boxleft" style="width:330px;padding-left:20px">
			<table>
				<tr><td width="60"><b>BC #</b></td><td><?=$ctrl->varvalue('bcno')?></td></tr>
				<tr><td><b>Name</b></td><td><?=$ctrl->varvalue('bcname')?></td></tr>
				<tr><td><b>Alamat</b></td><td><?=$ctrl->varvalue('bcaddress')?></td></tr>
			</table>
		</div>
	</div>

	<table class="dataview" width="500px">
		<tr>
			<th width="50" align="right">Kode</th>
			<th width="210" align="left">Nama Barang</th>
			<th width="80" align="right">Harga</th>
			<th width="80" align="right">Jumlah</th>
			<th width="80" align="right">Total</th>
		</tr>
		<?
			if (is_array($ctrl->items))
			{
				$i=0;
				foreach ($ctrl->items as $item)
				{
					echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
					echo "<td align=\"right\">" . $item['itemid'] . "</td>";
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
			<div class="boxleft1" style="width:410px">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Include PPN</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('includeppn'));?></div>
		</div>
	</div>
	
	<div style="width:495px;text-align:right">
		<button type="button" onclick="setaction('ok');" style="width:80px;">OK</button>
	</div>
</div>

<?include "bcfooterright.php";?>