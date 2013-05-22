<?include "bcheaderright.php";?>
<div id="cover"></div>
<div id="dialog" class="boxcon5" style="width:400px;padding-bottom:10px">
    <div class="boxcon6">
		Edit mode - Revisi order member ?
	</div>
	<div style="padding:10px 5px 15px 5px;">
		Total Stock pada product <?=$ctrl->varvalue('productrevisi')?> tidak mencukupi
		<br><br>
		Bila anda klik SETUJU, jumlah order member secara otomatis akan disesuaikan supaya stock produk mencukupi.
		Total order member akan berubah dari IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?> ke IDR <?=$ctrl->valuenumber($ctrl->varvalue('totalorderedited'));?>.
    </div>
	<div>
		<div class="boxleft">Anda ingin melanjutkan ?</div>
		<div class="boxright" style="padding-right:5px">
			<button type="button" onclick="closePopUp('dialog');" style="width:80px;">Kembali</button>
			<button type="button" onclick="setaction('validasi');" style="width:80px;">Setuju</button>
		</div>
	</div>
</div>
<input type="hidden" name="sc" id ="backpage" value="<?=$ctrl->value('sc')?>">
<input type="hidden" name="backpage" id ="backpage" value="<?=$ctrl->value('backpage')?>">
<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">
	 
<div class="boxcon" style="padding-bottom:5px; border-bottom:1px solid #9b9b9b;">
	<div class="boxleft" style="width:330px">
		<a href="bconlineorder.php">Online Orders</a> > Order #<?=$ctrl->value('salesid')?>
	</div>
	<div class="boxright" style="width:350px;padding-right:10px;">
		<div style="float:right;padding-left:7px"><?=$ctrl->colorstatuslabel($ctrl->status)?></div>
		<div class="color<?=$ctrl->colorstatus($ctrl->status)?>" style="float:right;margin-top:2px"></div>
	</div>
</div>

<div class="boxcon">
	<div class="boxleft" style="width:330px">
		<table>
			<tr><td width="100"><b>Member ID</b></td><td><?=$ctrl->varvalue('mbrno')?></td></tr>
			<tr><td><b>Name</b></td><td><?=$ctrl->varvalue('mbrname')?></td></tr>
			<tr><td><b>Mobile</b></td><td><?=$ctrl->varvalue('mbrmobile')?></td></tr>
			<tr><td valign="top"><b>Address</b></td><td><?=$ctrl->varvalue('mbraddress')?></td></tr>
		</table>
	</div>
	<div class="boxright" style="width:350px; text-align:right; padding-right:10px">
		Dibuat pada <?=$ctrl->varvalue('orderdate')?>
		<? if ($ctrl->varvalue('validatedate')) { ?><br>Validasi BC pada <?echo $ctrl->varvalue('validatedate'); } ?> 
		<? if ($ctrl->varvalue('paiddate')) { ?><br>Dibayar pada <?echo $ctrl->varvalue('paiddate'); } ?> 
		<? if ($ctrl->varvalue('canceldate')) { ?><br>Batal pada <?echo $ctrl->varvalue('canceldate'); } ?> 
		<? if ($ctrl->varvalue('deliverdate')) { ?><br>Dikirim pada <?echo $ctrl->varvalue('deliverdate'); } ?> 
	</div>
</div>
<div class="boxcon">
	<div class="boxcon6" style="width:190px; margin-left:325px;float:left;">Stock</div>
	<div class="boxcon6" style="width:160px; margin-right:10px;float:right;">Total</div>
</div>
<table class="dataview" >
	<tr>
		<th width="35" align="right">Kode</th>
		<th width="400" align="left">Nama Barang</th>
		<th width="60" align="right">Harga</th>
		<th width="30" align="right">Jumlah</th>	
		<th width="30" align="right">Sophie</th>
		<th width="30" align="right">BC</th>		
		<th width="30" align="right">Total</th>
		<th width="10" align="left">&nbsp;</th>
		<th width="80" align="right">Order MBR</th>
		<th width="80" align="right">Order BC</th>		

	</tr>
	<?
		if (is_array($ctrl->items))
		{
			$i=0;
			foreach ($ctrl->items as $item)
			{
				echo $i++%2?"<tr class=\"pinkrow\" >":"<tr>";
				echo "<td align=\"right\" height='25'>" . $item['itemid'] . "</td>";
				echo "<td align=\"left\">" . htmlspecialchars($item['itemname']). "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['price']) . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['salesqty']) . "</td>";		
				echo "<td align=\"right\"  height='25'>" . $ctrl->valuenumber($item['purchqty']) . "</td>";
				if ($ctrl->status == $ctrl->sysparam['salesstatus']['ordered']) 
				{
					?>
					<td align="right">
						<input type="hidden" name="itemid[]" id="itemid[]" value="<?=$item['itemid']?>">
						<input type="textbox" name="itemqty[]" id="itemqty[]" value="<?=$item['qtybc']?>" maxlength="3" style="width:30px;text-align:right" onkeypress="refresh(event);" onblur="refreshLostFocus();">
					</td>
					<?					
				} else {
					echo "<td align=\"right\">" . $ctrl->valuenumber($item['qtybc']) . "</td>";
				}				
				$color = '';
				if ($item['shortageqty'] != 0) $color = "color=\"FF0000\"";
								
				echo "<td align=\"right\">" . ($ctrl->valuenumber($item['purchqty']) + $ctrl->valuenumber($item['qtybc'])) . "</td>";
				
				if($ctrl->status == $ctrl->sysparam['salesstatus']['ordered'] ) { 	
					if ($item['salesqty'] == $item['purchqty'] + $item['qtybc'])
					{
						echo '<td><img src="images/ok.gif"/></td>';
					}
					else
					{
						echo '<td><img src="images/warning.gif"/></td>';
					}
				} 
				else
				{
					echo '<td>&nbsp;</td>';
				}
				echo '<td align="right"  height="25">' . $ctrl->valuenumber($item['totalordermember']) . '</td>';					
				echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalorderbc']) . "</td>";
				echo "</tr>";
			}
		}
		else
		{
			echo '<td colspan="10" align="center">no items</td>';
		}
	?>

</table>
<br>
<div class="boxcon">
	<div class="boxleft" style="width:340px;color:red;">
		<? if($ctrl->status == $ctrl->sysparam['salesstatus']['cancelled']) {?>
		Order ini telah ditolak karena <?=$ctrl->cancelreason($ctrl->cancelcode)?>		
		<? } ?>
		<? if($ctrl->status == $ctrl->sysparam['salesstatus']['ordered'] && $ctrl->varvalue('insufficientitems')) {?>
		<div class="boxleft" style="width:20px;"><img src="images/warning.gif"></div>
		<div class="boxleft" style="width:300px;margin-top:3px">Total stock product <?=$ctrl->varvalue('insufficientitems')?> tidak cukup.</div>
		<div class="boxleft" style="width:320px;margin-top:10px">
		Kalau lanjut, jumlah order member secara otomatis akan disesuakan supaya stock produk mencukupi. 
		<br>Setelah validasi, membernya akan diminta untuk konfirm order kembali sebelum pembayaran.
		</div>
		<? } ?>
	</div>
	<div class="boxright" style="width:350px;padding-right:1px;">
		<div class="boxcon1">
			<div class="boxleft1" style="width:200px">Total Order</div>
			<div class="boxright1" style="margin-left:1px"><?=$ctrl->valuenumber($ctrl->varvalue('totalorderbc'));?></div>
			<div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorder'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:200px">Discount</div>
			<div class="boxright1" style="margin-left:1px"><?=$ctrl->valuenumber($ctrl->varvalue('discountbc'));?></div>
			<div class="boxright1" ><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:200px">Total Bayar</div>
			<div class="boxright1-1" style="margin-left:1px"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayarbc'));?></div>
			<div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<? if ($ctrl->status >= $ctrl->sysparam['salesstatus']['edited'] && $ctrl->purchid <> "") { ?>
		<div class="boxcon1">
			<div class="boxleft1" style="width:200px">Tambahan Order BC</div>
			<div class="boxright1" style="width:130px;margin-right:0px"><?='<a href="bcviewmyorder.php?backpage=1&purchid=' . $ctrl->purchid .'">' . $ctrl->purchid . '</a>';?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:200px">Total Kredit Note BC</div>
			<div class="boxright1" style="width:130px;margin-right:0px"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar')-$ctrl->varvalue('totalbayarbc'));?></div>
		</div>
		<? } ?>
	</div>
</div>
<div style="width:703px;text-align:right">	
	<button class="back" type="button" onclick="setaction('cancel');" style="width:80px" >Kembali</button>
	<?
		switch ($ctrl->status)
		{
			case $ctrl->sysparam['salesstatus']['paid']: ?>
				<button type="button" onclick="setaction('ready');" style="width:80px;">Siap</button> <?
				break;
			case $ctrl->sysparam['salesstatus']['cancelled']: ?>
				<button type="button" onclick="setaction('clear');" style="width:80px;">Hapus</button> <?
				break;
			case $ctrl->sysparam['salesstatus']['ordered']: ?>
				<button type="button" onclick="<?if($ctrl->varvalue('productrevisi') == ''){?>setaction('validasi');<?} else {?>showPopUp('dialog');<?}?>" style="width:80px;">Validasi</button> <?
				break;
			case $ctrl->sysparam['salesstatus']['ready']: ?>
				<button type="button" onclick="setaction('delivered');" style="width:80px;">Delivered</button> <?
				break;
			case $ctrl->sysparam['salesstatus']['delivered']: ?>
				<button type="button" onclick="setaction('clear');" style="width:80px;">Clear</button> <?
				break;			
		}
	?>
</div>			
<?include "bcfooterright.php";?>
<script language="javascript">
	function refresh(e)
	{
		if (!e) e = window.event;   // resolve event instance
		if (e.keyCode == 13)
		{
			setaction('refresh');
		}
	}
	function refreshLostFocus()
	{
		setaction('refresh');
	}
</script>
