<?include "mbrheader.php";?>
<center>
	<div class="boxcon">
		<div class="boxleft" style="width:150px">
			<b>Member #<?=$ctrl->varvalue('mbrno')?></b>
			<br><?=$ctrl->varvalue('mbrname')?>
			<br><?=$ctrl->varvalue('mbraddress')?>
		</div>
		<div class="boxright" style="width:150px">
			<b>BC #<?=$ctrl->varvalue('bcno')?></b>
			<br><?=$ctrl->varvalue('bcname')?>
			<br><?=$ctrl->varvalue('bcaddress')?>
		</div>
	</div>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th width="20">Kode</th>
				<th width="200">Nama Barang</th>
				<th width="40" align="right">Harga</th>
				<th width="30" align="right">Jumlah</th>
				<th width="50" align="right">Total</th>
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
						?>
						<td>
							<input type="hidden" name="itemid[]" id="itemid[]" value="<?=$item['itemid']?>">
							<input type="textbox" name="itemqty[]" id="itemqty[]" value="<?=$item['qty']?>" maxlength="3" size="3" style="text-align:right">
						</td>
						<?
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
	</div>
	<div class="boxcon" style="text-align:right;">
	<button type="button" onclick="setaction('tambah');">Tambah Items</button>
	<button type="button" onclick="setaction('refresh');">Refresh</button>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
	</div>
	<br>
	<button type="button" onclick="setaction('confirm');">Konfirmasi</button>
</center>
<?include "mbrfooter.php";?>