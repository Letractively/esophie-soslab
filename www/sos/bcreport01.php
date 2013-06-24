<?include "bcheader.php";?>
<div class="boxcon4">
<div class="title">Stock BC On Hold</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">
<div class="boxcon">
	<div class="boxleft">
		<table>
			<tr><td>Member #&nbsp;</td><td>
				<div class="boxstyled1" style="width:200px; float:left;">
					<input type="text" id="search_kodemember" name="search_kodemember" value="<?=$ctrl->value("search_kodemember")?>" style="width:190px">
				</div>
			</td></tr>
			<tr><td>Status</td><td>
				<div class="boxstyled1" style="width:200px; float:left;">
					<select id="search_status" name="search_status" style="width:190px">
						<option value="4,5,6,7,8,9,0" <?=($ctrl->value("search_status") == "4,5,6,7,8,9,0" ? "selected" : "")?>>All
						<!--<option value="2" <?=($ctrl->value("search_status") == "2" ? "selected" : "")?>>On Order-->
						<!--<option value="3,4" <?=($ctrl->value("search_status") == "3,4" ? "selected" : "")?>>Dalam Proses-->
						<option value="4,5" <?=($ctrl->value("search_status") == "4,5" ? "selected" : "")?>>Belum Konfirmasi
						<option value="6,7" <?=($ctrl->value("search_status") == "6,7" ? "selected" : "")?>>Belum Bayar
						<option value="8,9" <?=($ctrl->value("search_status") == "8,9" ? "selected" : "")?>>Telah Bayar
						<option value="0" <?=($ctrl->value("search_status") == "0" ? "selected" : "")?>>Cancelled
                                                <!--<option value="10" <?=($ctrl->value("search_status") == "10	" ? "selected" : "")?>>Delivered-->
					</select>
				</div>
			</td></tr>
		</table>
		<br>
		<button type="button" onclick="setaction('search');" style="width:60px;">Search</button>&nbsp;&nbsp;
		<button type="button" onclick="setaction('reset');" style="width:60px;">Clear</button>	
	</div>
	<div class="boxright">
		<div class="boxcon5" style="width:300px;margin-right:25px;">
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
		<th width="70" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemid');">Kode Item<?=$ctrl->sortimage('itemid')?></a></th>
		<th width="50" align="right"><a href="#" class="colname" onclick="setaction('sortby_qtybc');">Jumlah<?=$ctrl->sortimage('qtybc')?></a></th>
		<th width="190" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemname');">Nama Item<?=$ctrl->sortimage('itemname')?></a></th>		
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Online Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="300" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member<?=$ctrl->sortimage('kodemember')?></a></th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_statusname');">Status<?=$ctrl->sortimage('statusname')?></a></th>
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
			echo '<td align="left"><a ' . ($i%2?'class="grid"':'') . ' href="bcvieworder.php?backpage=1&salesid=' . urlencode($item['salesid']) . '&sc=' . $ctrl->searchcriteria . '">'. $item['salesid'] . '</a></td>';	
			echo '<td align="left"> #' . htmlspecialchars($item['kodemember']). ' ' . $item['namamember'] .'</td>';
			echo '<td align="left">' . htmlspecialchars($item['statusname']). '</td>';
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