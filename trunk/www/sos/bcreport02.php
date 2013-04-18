<?include "bcheader.php";?>
<div class="boxcon4">
<div class="title">Search Orders History</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">
<div class="boxcon">
	<div class="boxleft">
		<table>
			<tr><td>Online order #</td><td><input type="text" id="search_salesid" name="search_salesid" value="<?=$ctrl->value("search_salesid")?>"></td></tr>
			<tr><td>Tanggal <small>(dd/mm/yyyy)</small></td><td><input type="text" id="search_orderdate_from" name="search_orderdate_from" value="<?=$ctrl->value("search_orderdate_from")?>" placeholder="dd/mm/yyyy"> to <input type="text" id="search_orderdate_to" name="search_orderdate_to" value="<?=$ctrl->value("search_orderdate_to")?>" placeholder="dd/mm/yyyy"></td></tr>
			<tr><td>Member #</td><td><input type="text" id="search_kodemember" name="search_kodemember" value="<?=$ctrl->value("search_kodemember")?>"></td></tr>
			<!--<tr><td>Member name</td><td><input type="text" id="search_namamember" name="search_namamember" value="<?=$ctrl->value("search_namamember")?>"></td></tr>-->
			<tr><td>Order BC #</td><td><input type="text" id="search_salesidsmi" name="search_salesidsmi" value="<?=$ctrl->value("search_salesidsmi")?>"></td></tr>
			<tr><td>Status</td><td>
				<select id="search_status" name="search_status">
					<option value="" <?=($ctrl->value("search_status") == "" ? "selected" : "")?>>All
					<option value="10" <?=($ctrl->value("search_status") == "10" ? "selected" : "")?>>Clear
					<option value="0" <?=($ctrl->value("search_status") == "0" ? "selected" : "")?>>Cancelled
				</select>
			</td></tr>
		</table>
	</div>
	<div class="boxright">
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

<button type="button" onclick="if (validsearch()){setaction('search');};" style="width:60px;">Search</button>&nbsp;&nbsp;
<button type="button" onclick="setaction('reset');" style="width:60px;">Clear</button>	
<br><br>
<table class="dataview">
	<tr>
		<th>&nbsp;</th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="110" align="left"><a href="#" class="colname" onclick="setaction('sortby_orderdate');">Tanggal Order<?=$ctrl->sortimage('orderdate')?></a></th>
		<th width="60" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member<?=$ctrl->sortimage('kodemember')?></a></th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_namamember');">Member Name<?=$ctrl->sortimage('namamember')?></a></th>
		<th width="110" align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayar');">Total<?=$ctrl->sortimage('totalbayar')?></a></th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesidsmi');">Order BC<?=$ctrl->sortimage('salesidsmi')?></a></th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_statusname');">Status<?=$ctrl->sortimage('statusname')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i++%2?'<tr class="pinkrow">':'<tr>';
			echo '<td><div class="color' . $ctrl->colorstatus($item['status']) . '"></div></td>';			
			echo '<td align="left"><a href="bcvieworder.php?backpage=2&salesid=' . $item['salesid'] .'">' . $item['salesid'] . '</a></td>';
			echo '<td align="left">' . $item['orderdate']. '</td>';
			echo '<td align="left">' . htmlspecialchars($item['kodemember']). '</td>';
			echo '<td align="left">' . htmlspecialchars($item['namamember']). '</td>';
			echo '<td align="right">' . $ctrl->valuenumber($item['totalbayar']) . '</td>';
			echo '<td align="left"><a href="bcviewmyorder.php?backpage=2&purchid=' . $item['salesid'] .'">' . $item['salesidsmi'] . '</a></td>';
			//echo '<td align="left">' . htmlspecialchars($item['salesidsmi']). '</td>';
			echo '<td align="left">' . htmlspecialchars($item['statusname']). '</td>';
			echo '</tr>';
		}
	}
	else
	{
		echo '<td colspan="8" align="center">no items</td>';
	}
?>
</table>
</div>
<?include "bcfooter.php";?>

<script language="javascript">
function validsearch()
{
	var ret = true;
	if (!isvaliddate('search_orderdate_from')) ret = checkfailed ('Date from salah format');
	if (ret && !isvaliddate('search_orderdate_to')) ret = checkfailed ('Date to salah format');	
	return ret;
}
</script>