<?include "bcheaderright.php";?>
<div class="boxcon">
	<input type="hidden" name="sc" id ="backpage" value="<?=$ctrl->value('sc')?>">
	<input type="hidden" name="backpage" id ="backpage" value="<?=$ctrl->value('backpage')?>">
	<input type="hidden" name="purchid" id ="purchid" value="<?=$ctrl->value('purchid')?>">
	<div class="boxcon" style="border-bottom:1px solid black;">
		<div class="boxleft" style="width:380px">
			<a href="bconlineorder.php">Online Orders</a> > <a href="#" onclick="setaction('bcorder');"> Order #<?=$ctrl->value('purchid')?></a> > Tambahan Order BC
		</div>
		<div class="boxright" style="width:300px;padding-right:10px;">
			<div style="float:right;padding-left:7px"><?=$ctrl->colorstatuslabel($ctrl->status)?></div>
			<div class="color<?=$ctrl->colorstatus($ctrl->status)?>" style="float:right;margin-top:2px"></div>
		</div>
	</div>
	<div class="boxcon">
		<div class="boxleft" style="width:250px">
			<b>TAMBAHAN ORDER BC</b><br>
			PT. Sophie Paris Indonesia<br>
			Telp. 021 8312389<br>
		</div>
		<div class="boxright" style="width:240px">
			<table style="width:230px">
				<tr><td width="100"><b>No. Order BC</b></td><td align="right"><?=$ctrl->varvalue('salesidsmi')?></td></tr>
				<tr><td><b>No Request</b></td><td align="right"><a href="bcvieworder.php?salesid=<?=$ctrl->varvalue('purchid')?>"><?=$ctrl->varvalue('purchid')?></a></td></tr>
				<tr><td><b>ID</b></td><td align="right">#<?=$ctrl->varvalue('bcno')?></td></tr>
				<tr><td><b>Nama BC</b></td><td align="right"><?=$ctrl->varvalue('bcname')?></td></tr>
				<tr><td><b>Tanggal</b></td><td align="right"><?=$ctrl->varvalue('orderdate')?></td></tr>
			</table>
		</div>
	</div>

	<table class="dataview" width="700px">
		<tr>
			<th width="10" align="right">No</th>
			<th width="40">Ref #</th>
			<th width="40" align="right">Jumlah</th>
			<th>Nama Barang</th>
			<th width="80" align="right">Harga BC</th>
			<th width="80" align="right">Hg. Satuan</th>
			<th width="80" align="right">TOTAL</th>
		</tr>
		<?
			if (is_array($ctrl->items))
			{
				$i=0;
				foreach ($ctrl->items as $item)
				{
					echo $i++%2?'<tr class="pinkrow">':'<tr>';
					echo '<td align="right">' . $i . '. </td>';
					echo '<td align="left">' . $item['itemid'] . '</td>';
					echo '<td align="right">' . $ctrl->valuenumber($item['qty']) . '</td>';
					echo '<td align="left">' . htmlspecialchars($item['itemname']). '</td>';
					echo '<td align="right">' . $ctrl->valuenumber($item['pricebc']) . '</td>';
					echo '<td align="right">' . $ctrl->valuenumber($item['price']) . '</td>';
					echo '<td align="right">' . $ctrl->valuenumber($item['totalorder']) . '</td>';
					echo '</tr>';
				}
			}
			else
			{
				echo '<td colspan="5" align="center">no items</td>';
			}
		?>
	</table>
	<br>
	<div class="boxcon" style="width:695px">
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">SUB TOTAL</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">DISCOUNT</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">TOTAL FAKTUR</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:610px">PPN INCLUDE</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('includeppn'));?></div>
		</div>
	</div>
	<div style="width:695px;text-align:right">
		<button class="back" type="button" onclick="setaction('cancel');" style="width:80px" >Kembali</button>
	</div>
</div>
<?include "bcfooterright.php";?>