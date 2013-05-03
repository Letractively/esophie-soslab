<?include "bcheader.php";?>
<div class="boxcon4">
<div class="title">Stock BC On Hold</div>
<div class="boxcon">
<div class="boxleft">
	<div class="boxcon5" style="width:280px;margin-right:25px;">
		<table width="100%">
			<tr>
				<td width="12px"><div class="color01"></div></td><td width="140"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ordered'])?></td>
				<td><div class="color08"></div></td><td width="140"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['paid'])?></td>
			</tr>
			<tr>
				<td width="12px"><div class="color02"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['bypassed'])?></td>
				<td><div class="color09"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ready'])?></td>
			</tr>
			<tr>	
				<td width="12px"><div class="color05"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['edited'])?></td>
				<td><div class="color10"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['delivered'])?></td>
			</tr>
			<tr>
				<td width="12px"><div class="color06"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['validated'])?></td>
				<td><div class="color00"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['cancelled'])?></td>
			</tr>
		</table>
	</div>
</div>
</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">
<table class="dataview">
	<tr>
		<th>&nbsp;</th>
		<th width="50" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemid');">Kode Item<?=$ctrl->sortimage('itemid')?></a></th>
		<th width="50" align="right"><a href="#" class="colname" onclick="setaction('sortby_qtybc');">Jumlah<?=$ctrl->sortimage('qtybc')?></a></th>
		<th width="180" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemname');">Nama Item<?=$ctrl->sortimage('itemname')?></a></th>		
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Online Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="300" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member<?=$ctrl->sortimage('kodemember')?></a></th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_status');">Status<?=$ctrl->sortimage('status')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i%2?'<tr class="pinkrow">':'<tr>';
			echo '<td><div class="color' . $ctrl->colorstatus($item['status']) . '"></div></td>';
			echo '<td align="left">' . $item['itemid'] . '</td>';
			echo '<td align="right">' . $ctrl->valuenumber($item['qtybc']) . '</td>';
			echo '<td align="left">' . htmlspecialchars($item['itemname']). '</td>';			
			echo '<td align="left"><a ' . ($i%2?'class="grid"':'') . ' href="bcvieworder.php?salesid=' . urlencode($item['salesid']) . '">'. $item['salesid'] . '</a></td>';	
			echo '<td align="left"> #' . htmlspecialchars($item['kodemember']). ' ' . $item['namamember'] .'</td>';
			echo '<td align="left">' . htmlspecialchars($item['status']). '</td>';
			echo '</tr>';
			$i++;
		}
	}
	else
	{
		echo '<td colspan="6" align="center">no items on hold</td>';
	}
?>
</table>
</div>
<?include "bcfooter.php";?>