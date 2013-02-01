<?include "bcheaderright.php";?>
<br>
<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">
<div class="boxcon">
	<div class="boxleft" style="width:330px">
		<table>
			<tr><td width="60"><b>Order#</b></td><td><?=$ctrl->value('salesid')?></td></tr>
			<tr><td><b>Status</b></td><td><?=$ctrl->varvalue('status')?></td></tr>
			<tr><td><b>Tanggal</b></td><td><?=$ctrl->varvalue('orderdate')?></td></tr>
		</table>
	</div>
	<div class="boxright" style="width:330px">
		<table>
			<tr><td width="90"><b>Member ID</b></td><td><?=$ctrl->varvalue('mbrno')?></td></tr>
			<tr><td><b>Member Name</b></td><td><?=$ctrl->varvalue('mbrname')?></td></tr>
		</table>
	</div>
</div>
<table class="dataview">
	<tr>
		<th width="40" align="right">Kode</th>
		<th width="150" align="left">Nama Barang</th>
		<th width="60" align="right">Harga</th>
		<th width="40" align="right">Jumlah</th>
		<th width="80" align="right">Total MBR</th>
		<th width="80" align="right">Stock Saya</th>
		<th width="55" align="right">Order +</th>
		<th width="50" align="right">Order -</th>
		<th width="50" align="right">Total +</th>
	</tr>
	<?
		if (is_array($ctrl->items))
		{
			$i=0;
			foreach ($ctrl->items as $item)
			{
				echo $i++%2?"<tr class=\"pink\">":"<tr>";
				echo "<td align=\"right\">" . $item['itemid'] . "</td>";
				echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['salesqty']) . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalbayarmember']) . "</td>";
				?>
				<td align="center">
					<input type="hidden" name="itemid[]" id="itemid[]" value="<?=$item['itemid']?>">
					<input type="textbox" name="itemqty[]" id="itemqty[]" value="<?=$item['qtybc']?>" maxlength="3" style="width:30px;text-align:right">
				</td>
				<?
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['purchqty']) . "</td>";
				$color = '';
				if ($item['shortageqty'] != 0) $color = "color=\"FF0000\"";
				echo "<td align=\"right\"><font " . $color . ">" . $ctrl->valuenumber($item['shortageqty']) . "</font></td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalbayarbc']) . "</td>";
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
<div class="boxcon">
	<div class="boxleft" style="width:421px">
		<div class="boxcon1">
			<div class="boxleft1" style="width:340px">Total Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:340px">Total Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:340px">Additional Charge (<?=$ctrl->varvalue("paymentname")?>)</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('paymentcharge'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:340px">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
	</div>
	<div class="boxleft" style="width:80px">
		<div class="boxcon1">
			<button type="button" onclick="setaction('refresh');" style="width:80px;">Refresh</button>
		</div>
	</div>
	<div class="boxright" style="width:180px;padding-right:9px">	
		<div class="boxcon1">
			<div class="boxleft1" style="width:100px">Total BC</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayarbc'));?></div>
		</div>
	</div>
</div>
<div style="text-align:right">
	<button type="button" onclick="setaction('cancel');" style="width:80px;">Cancel</button>
	<button type="button" onclick="setaction('ok');" style="width:80px;">OK</button>
</div>
<?include "bcfooterright.php";?>