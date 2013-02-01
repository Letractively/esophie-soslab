<?include "mbrheader.php";?>
	<?if ($ctrl->pageview == 'edited') { ?>
		<h2>Order Edited</h2>
	<? }else { ?>
		<h2>Konfirmasi Order</h2>
	<? } ?>
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
	<?if ($ctrl->pageview != "openorder") { ?>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th>order #</th>
				<th>Date Time</th>
				<th>Status</th>
				<th>Sisa Waktu</th>
			</tr>
			<tr>
				<td><?=$ctrl->varvalue('salesid')?></td>
				<td><?=$ctrl->varvalue('orderdate')?></td>
				<td><?=$ctrl->varvalue('status')?></td>
				<td><?=$ctrl->varvalue('timeleft')?></td>
			</tr>
		</table>
	</div>
	<? } ?>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th width="30">Kode</th>
				<th width="230">Nama Barang</th>
				<th width="50" align="right">Harga</th>
				<th width="30" align="right">Jumlah</th>
				<?if ($ctrl->pageview == 'edited') { ?> <th width="30" align="right">Tersedia</th> <?}?>
				<th width="70" align="right">Total</th>
			</tr>
			<?
				if (is_array($ctrl->items))
				{
					$i=0;
					foreach ($ctrl->items as $item)
					{					
						echo $i++%2?"<tr class=\"pink\">":"<tr>";
						echo "<td align=\"left\">" . $item['itemid'] . "</td>";
						echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
						echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";
						echo "<td align=\"right\">" . $ctrl->valuenumber($item['qty']) . "</td>";
						if ($ctrl->pageview == 'edited') { echo "<td align=\"right\">" . $ctrl->valuenumber($item['qtyavail']) . "</td>"; }
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
	<div class="boxcon1">
		<div class="boxleft1">Total Order</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Discount Member</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Payment Charge</div><div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('paymentcharge'));?></div>
	</div>
	<div class="boxcon1">
		<div class="boxleft1">Total Pembayaran</div><div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
	</div>
	<br>
	<?if ($ctrl->pageview == 'openorder') { ?>
		<button type="button" onclick="setaction('sendordertobc');">Kirim Order ke BC</button>
	<? } ?>
	<?if ($ctrl->pageview == 'edited') { ?>
		<button type="button" onclick="setaction('cancel');">Cancel Order</button>
		<button type="button" onclick="setaction('confirm');">Confirm</button>
	<? } ?>
	<?if ($ctrl->pageview == 'validate') { ?>
		<button type="button" onclick="setaction('pembayaran');">Pembayaran</button>
	<? } ?>
			
<?include "mbrfooter.php";?>