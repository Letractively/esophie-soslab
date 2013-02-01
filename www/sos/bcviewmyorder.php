<?include "bcheaderright.php";?>
<div class="boxcon" style="width:510px;padding-left:100px">
	<input type="hidden" name="purchid" id ="purchid" value="<?=$ctrl->value('purchid')?>">
	<div class="boxcon">
		<div class="boxleft" style="width:250px">
			<b>Sophie</b><br>
			&nbsp;&nbsp;Sophie paris building<br>
			&nbsp;&nbsp;Jl.Adyaksa RayaNo. 33<br>
			&nbsp;&nbsp;12440 - Jakarta<br>			
			<table border="0" cellspacing="0">
				<tr><td width="70" align="left"><b>Status</b></td><td><?=$ctrl->varvalue('status')?></td></tr>
			</table>
		</div>
		<div class="boxright" style="width:220px">
			<table>
				<tr><td width="70"><b>No. Faktur</b></td><td width="120" align="left"><?=$ctrl->varvalue('salesidsmi')?></td></tr>
				<tr><td><b>Kode BC</b></td><td align="left"><?=$ctrl->varvalue('bcno')?></td></tr>
				<tr><td><b>Nama BC</b></td><td align="left"><?=$ctrl->varvalue('bcname')?></td></tr>
				<tr><td><b>Tanggal</b></td><td align="left"><?=$ctrl->varvalue('orderdate')?></td></tr>
			</table>
		</div>
	</div>

	<table class="dataview" width="500px">
		<tr>
			<th width="40">Kode</th>
			<th width="200">Nama Barang</th>
			<th width="60">Harga</th>
			<th width="40">Jumlah</th>
			<th width="60">Total</th>
		</tr>
		<?
			if (is_array($ctrl->items))
			{
				$i=0;
				foreach ($ctrl->items as $item)
				{
					echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
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
			<div class="boxleft1" style="width:410px">Sub Total</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Discount</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Total Bayar</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:410px">Include PPN</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('includeppn'));?></div>
		</div>
	</div>
	<div style="width:495px;text-align:right">
		<button type="button" onclick="setaction('cancel');" style="width:80px;">Cancel</button>
	</div>
</div>
<?include "bcfooterright.php";?>