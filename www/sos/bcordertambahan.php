<?include "bcheaderright.php";?>
<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">

<div class="boxcon" style="border-bottom:1px solid black;">
	<div class="boxleft" style="width:480px">
		Online Orders > Order #<?=$ctrl->value('salesid')?> > Tambahan Order BC
	</div>
	<div class="boxright" style="width:200px;padding-right:10px;">
		<div style="float:right;padding-left:7px"><?=$ctrl->colorstatuslabel($ctrl->status)?></div>
		<div class="color<?=$ctrl->colorstatus($ctrl->status)?>" style="float:right;margin-top:2px"></div>
	</div>
</div>

<div class="boxcon">	
	<div class="boxcon">
		<div class="boxleft" style="width:330px;">
			<b>TAMBAHAN ORDER BC</b>
			<br>PT. Sophie Paris Indonesia
			<br>Telp. 021 8312389
		</div>
		<div class="boxright" style="width:300px;align:right;">
			<table>
				<tr><td width="150"><b>No. Request</b></td><td  width="150" align="right">Belum Ada</td></tr>
				<tr><td><b>No. Online Number</b></td><td align="right"><?=$ctrl->value('salesid')?></td></tr>
				<tr><td><b>ID</b></td><td align="right"><?=$ctrl->varvalue('bcno')?></td></tr>
				<tr><td><b>Name</b></td><td align="right"><?=$ctrl->varvalue('bcname')?></td></tr>
				<tr><td><b>Tanggal</b></td><td align="right"><?=$ctrl->varvalue('orderdate')?></td></tr>
			</table>
		</div>
	</div>

	<table class="dataview">
		<tr>
			<th width="20" align="right">No</th>
			<th width="50" align="right">Kode</th>
			<th width="80" align="right">Jumlah</th>
			<th width="200" align="left">Nama Barang</th>
			<th width="90" align="right">Harga BC</th>
			<th width="90" align="right">Harga Satuan</th>			
			<th width="90" align="right">Total</th>
		</tr>
		<?
			if (is_array($ctrl->items))
			{
				$i=0;
				foreach ($ctrl->items as $item)
				{
					echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
					echo "<td align=\"right\">" . $i . "</td>";
					echo "<td align=\"right\">" . $item['itemid'] . "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['qty']) . "</td>";
					echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['pricebc']) . "</td>";
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";					
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
	<div class="boxcon" style="width:705px">
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">Subtotal</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">Discount</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">Total Faktur</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">PPN Include</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('includeppn'));?></div>
		</div>
	</div>
	
	<div style="width:705px;text-align:right">
		<button type="button" onclick="setaction('kembali');" style="width:80px;">Kembali</button>
		<button type="button" onclick="setaction('setuju');" style="width:80px;">Setuju</button>
	</div>
</div>

<?include "bcfooterright.php";?>