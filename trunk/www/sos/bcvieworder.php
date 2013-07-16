<?include "bcheaderright.php";?>
<div id="cover"></div>
<div id="dialog" class="boxcon5" style="width:400px;padding-bottom:10px">
    <? 
    $popupaction = '';
    if($ctrl->status == $ctrl->sysparam['salesstatus']['ordered']) { 
        $popupaction = 'validasi'; ?>
    <div class="boxcon6">Revisi order member ?</div>
    <div style="padding:10px 5px 15px 5px;">
            Total stock pada product <?=$ctrl->varvalue('productrevisi')?> tidak mencukupi!<br><br>
            Bila anda klik LANJUT, order member secara otomatis akan disesuaikan supaya stock produk mencukupi.
    </div>
    <? } else if($ctrl->status == $ctrl->sysparam['salesstatus']['cancelled']) { 
        $popupaction = 'clear'; ?>
    <div class="boxcon6">Clear cancel order ?</div>
    <div style="padding:10px 5px 15px 5px;">
            Order ini telah dibatalkan. Silahkan release stock BC anda sebelum klik LANJUT.
    </div>
    <? } else if($ctrl->status == $ctrl->sysparam['salesstatus']['paid']) { 
        $popupaction = 'ready'; ?>
    <div class="boxcon6">Ready to pickup?</div>
    <div style="padding:10px 5px 15px 5px;">
            Bila anda klik LANJUT, member secara otomatis akan menerima email dan SMS untuk dikasih tahu order sudah siap diambil di BC.
    </div>
    <? } else if($ctrl->status == $ctrl->sysparam['salesstatus']['ready']) {
        $popupaction = 'delivered';?>
    <div class="boxcon6">Order delivered?</div>
    <div style="padding:10px 5px 15px 5px;">
            Jangan lupa melakukan invoicing kepada member dan reporting F1 sebelum klik LANJUT.
    </div>
    <? } ?>
    <div>
            <div class="boxleft">Anda ingin melanjutkan ?</div>
            <div class="boxright" style="padding-right:5px">
                    <button type="button" onclick="closePopUp('dialog');" style="width:80px;">Kembali</button>
                    <button type="button" onclick="setaction('<?= $popupaction ?>');" style="width:80px;">Lanjut</button>
            </div>
    </div>
</div>

<input type="hidden" name="sc" id ="sc" value="<?=$ctrl->value('sc')?>">
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
	<div class="boxright" style="width:350px; text-align:right; padding-right:10px"><? 
            echo "Dibuat pada " . $ctrl->varvalue('orderdate');
            if ($ctrl->varvalue('validatedate'))    echo "<br/>Validasi BC pada " . $ctrl->varvalue('validatedate'); 
            if ($ctrl->varvalue('paiddate'))        echo "<br/>Dibayar pada " . $ctrl->varvalue('paiddate'); 
            if ($ctrl->varvalue('canceldate'))      echo "<br/>Batal pada " . $ctrl->varvalue('canceldate'); 
            if ($ctrl->varvalue('deliverdate'))     echo "<br/>Dikirim pada " . $ctrl->varvalue('deliverdate');
	?></div>
</div>
<table width="714">
	<tr>
		<td>
			<div class="tabletitle">ORDER MEMBER</div>
                        <table class="dataview" >
				<tr>
					<th align="right">Kode</th>
					<th align="left">Nama Barang</th>
					<th align="right">Harga</th>
					<th align="right">Jumlah</th>
				</tr>
				<?
					if (is_array($ctrl->items))
					{
						$i=0;
						foreach ($ctrl->items as $item)
						{
							//$item['itemname'] = '1234567890123456789012345678901234567890';
							echo $i++%2?"<tr class=\"pinkrow\" >":"<tr>";
							echo "<td align=\"right\" width=\"35\" height='25'>" . $item['itemid'] . "</td>";
							echo "<td align=\"left\" width=\"180\" title=\"".  htmlspecialchars($item['itemname']) ."\">" . (strlen(htmlspecialchars($item['itemname'])) >= 23 ? substr((htmlspecialchars($item['itemname'])),0,20)."..." : htmlspecialchars($item['itemname']))  ."</td>";
							echo "<td align=\"right\" width=\"60\">" . $ctrl->valuenumber($item['price']) . "</td>";
							echo "<td align=\"right\" width=\"*\">" . $ctrl->valuenumber($item['salesqty']) . "</td>";		
							echo "</tr>";
						}
					}
					else
					{
						echo '<td colspan="4" align="center">no items</td>';
					}
				?>
			</table>
		</td>
		<td>
                        <div class="tabletitle">STOCK</div>
                        <table class="dataview">
				<tr>
					<th align="center">Sophie</th>
					<th align="center">BC</th>		
					<th align="center">Total</th>
				</tr>
				<?
					if (is_array($ctrl->items))
					{
						$i=0;
						foreach ($ctrl->items as $item)
						{
							echo $i++%2?"<tr class=\"pinkrow\" >":"<tr>";
							echo "<td align=\"center\" height='25' width=\"40\">" . $ctrl->valuenumber($item['purchqty']) . "</td>";
							if ($ctrl->status == $ctrl->sysparam['salesstatus']['ordered']) 
							{
								?>
								<td align="center" width="40">
									<input type="hidden" name="itemid[]" id="itemid[]" value="<?=$item['itemid']?>">
									<input type="textbox" name="itemqty[]" id="itemqty[]" value="<?=$item['qtybc']?>" maxlength="3" style="width:30px;text-align:right" onkeypress="refresh(event);" onblur="refreshLostFocus();">
								</td>
								<?					
							} else {
								echo "<td align=\"center\" width=\"40\">" . $ctrl->valuenumber($item['qtybc']) . "</td>";
							}				
							$color = '';
							if ($item['shortageqty'] != 0) $color = "color=\"FF0000\"";
											
							echo "<td align=\"center\"  width=\"40\">" . ($ctrl->valuenumber($item['purchqty']) + $ctrl->valuenumber($item['qtybc'])) . "</td>";
							echo "</tr>";
						}
					}
					else
					{
						echo '<td colspan="3" width=\"180\" align="center">no items</td>';
					}
				?>
			</table>
		</td>
		<td>
			<div class="tabletitle">TOTAL</div>
                        <table class="dataview">
				<tr>
					<th align="right">Member</th>
					<th align="right">BC</th>		
				</tr>
				<?
					if (is_array($ctrl->items))
					{
						$i=0;
						foreach ($ctrl->items as $item)
						{
							echo $i++%2?"<tr class=\"pinkrow\" >":"<tr>";
							echo '<td align="right"  height="25" width="60" >' . $ctrl->valuenumber($item['totalordermember']) . '</td>';					
							echo "<td align=\"right\"  width=\"60\">" . $ctrl->valuenumber($item['totalorderbc']) . "</td>";
							echo "</tr>";
						}
					}
					else
					{
						echo '<td colspan="2" align="center">no items</td>';
					}
				?>
			</table>
		</td>
	</tr>
</table>
<br>
<div class="boxcon">
	<? if($ctrl->status == $ctrl->sysparam['salesstatus']['cancelled']) {?>
        <div class="errorbox">
		Order ini telah ditolak karena <?=$ctrl->cancelreason($ctrl->cancelcode)?>
        </div>
	<? } ?>
	<? if($ctrl->status == $ctrl->sysparam['salesstatus']['ordered']  &&
                ($ctrl->varvalue('insufficientitems') || !$ctrl->isvalidhours) ) { ?>
        <div class="errorbox" style="width:330px;"> <?
            if ($ctrl->varvalue('insufficientitems')) 
            { ?>
            <div class="boxleft" style="width:20px;"><img src="images/warning.gif"></div>
            <div class="boxleft" style="color:red;width:280px;margin-top:3px;margin-bottom:5px;">Total stock product <?=$ctrl->varvalue('insufficientitems')?> tidak cukup.</div>
            <div class="boxleft" style="width:320px;margin-bottom:5px;">
            Kalau lanjut, jumlah order member secara otomatis akan disesuakan supaya stock produk mencukupi. 
            <br>Setelah validasi, membernya akan diminta untuk konfirm order kembali sebelum pembayaran.
            </div>
        <?  } 
            if (!$ctrl->isvalidhours) 
            { ?>
            <div class="boxleft" style="width:20px;"><img src="images/warning.gif"></div>
            <div class="boxleft" style="color:red;width:280px;margin-top:3px;margin-bottom:5px;"><?=$ctrl->errmsg;?></div>
	<?  } ?>
        </div>
        <? } ?>
	
	<div class="boxright" style="width:300px;padding-right:4px;">
		<div class="boxcon1">
			<div class="boxleft1" style="width:145px">Total Order</div>
			<div class="boxright1" style="margin-left:5px"><?=$ctrl->valuenumber($ctrl->varvalue('totalorderbc'));?></div>
			<div class="boxright1"><?=$ctrl->valuenumber($ctrl->varvalue('totalorderedited'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:145px">Discount</div>
			<div class="boxright1" style="margin-left:5px"><?=$ctrl->valuenumber($ctrl->varvalue('discountbc'));?></div>
			<div class="boxright1" ><?=$ctrl->valuenumber($ctrl->varvalue('discount'));?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:145px">Total Bayar</div>
			<div class="boxright1-1" style="margin-left:5px"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayarbc'));?></div>
			<div class="boxright1-1"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar'));?></div>
		</div>
		<? if ($ctrl->status >= $ctrl->sysparam['salesstatus']['edited'] && $ctrl->purchid <> "") { ?>
		<div class="boxcon1">
			<div class="boxleft1" style="width:145px">Tambahan Order BC</div>
			<div class="boxright1" style="width:134px;margin-right:0px"><?='<a href="bcviewmyorder.php?backpage=1&purchid=' . $ctrl->purchid .'">' . ($ctrl->salesidsmi != '' ? $ctrl->salesidsmi : '&nbsp;'). '</a>';?></div>
		</div>
		<div class="boxcon1">
			<div class="boxleft1" style="width:145px">Total Kredit Note BC</div>
			<div class="boxright1" style="width:134px;margin-right:0px"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayar')-$ctrl->varvalue('totalbayarbc'));?></div>
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
				<button type="button" onclick="showPopUp('dialog');" style="width:80px;">Siap</button> <?
				break;
			case $ctrl->sysparam['salesstatus']['cancelled']: 
				if (!$ctrl->iscleared)
                                { ?>
                                <button type="button" onclick="showPopUp('dialog');" style="width:80px;">Hapus</button> <?
                                }
				break;
			case $ctrl->sysparam['salesstatus']['ordered']: 
                                if ($ctrl->isvalidhours) { ?>
                                <button type="button" onclick="<?if($ctrl->varvalue('productrevisi') == ''){?>setaction('validasi');<?} else {?>showPopUp('dialog');<?}?>" style="width:80px;">Validasi</button> <? }
				break;
			case $ctrl->sysparam['salesstatus']['ready']: ?>
				<button type="button" onclick="showPopUp('dialog');" style="width:80px;">Delivered</button> <?
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
