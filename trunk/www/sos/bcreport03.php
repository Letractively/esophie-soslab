<?include "bcheader.php";?>
<div class="boxcon4">
<div class="title">Kredit BC Rekap</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">

<div class="boxcon">
	<div class="boxleft">
		<table>
			<tr>
				<td>Tgl Bayar <small>(dd/mm/yyyy)</small></td>
				<td><input type="text" id="search_paiddate_from" name="search_paiddate_from" value="<?=$ctrl->value("search_paiddate_from")?>" placeholder="dd/mm/yyyy"> to <input type="text" id="search_paiddate_to" name="search_paiddate_to" value="<?=$ctrl->value("search_paiddate_to")?>" placeholder="dd/mm/yyyy"></td>
			</tr>
		</table><br>
		<button type="button" onclick="if (validsearch()){setaction('search');};" style="width:60px;">Search</button>&nbsp;&nbsp;
		<button type="button" onclick="setaction('reset');" style="width:60px;">Clear</button>	
<? /* 
<table>
	<tr><td>Online order #</td><td><input type="text" id="search_salesid" name="search_salesid" value="<?=$ctrl->value("search_salesid")?>"></td></tr>
	<tr><td>Date <small>(dd/mm/yyyy)</small></td><td><input type="text" id="search_paiddate_from" name="search_paiddate_from" value="<?=$ctrl->value("search_paiddate_from")?>" placeholder="dd/mm/yyyy"> to <input type="text" id="search_paiddate_to" name="search_paiddate_to" value="<?=$ctrl->value("search_paiddate_to")?>" placeholder="dd/mm/yyyy"></td></tr>
	<tr><td>Member #</td><td><input type="text" id="search_kodemember" name="search_kodemember" value="<?=$ctrl->value("search_kodemember")?>"></td></tr>
	<tr><td>Member name</td><td><input type="text" id="search_namamember" name="search_namamember" value="<?=$ctrl->value("search_namamember")?>"></td></tr>
	<tr><td>BC Sales Order #</td><td><input type="text" id="search_salesidsmi" name="search_salesidsmi" value="<?=$ctrl->value("search_salesidsmi")?>"></td></tr>
	<tr><td>Status</td><td>
		<select id="search_status" name="search_status">
			<option value="" <?=($ctrl->value("search_status") == "" ? "selected" : "")?>>All
			<option value="1" <?=($ctrl->value("search_status") == "1" ? "selected" : "")?>>Open Order
			<option value="2" <?=($ctrl->value("search_status") == "2" ? "selected" : "")?>>Ordered
			<option value="3" <?=($ctrl->value("search_status") == "3" ? "selected" : "")?>>On Order
			<option value="9" <?=($ctrl->value("search_status") == "9" ? "selected" : "")?>>Delivered
			<option value="10" <?=($ctrl->value("search_status") == "10" ? "selected" : "")?>>Clear
			<option value="0" <?=($ctrl->value("search_status") == "0" ? "selected" : "")?>>Cancelled
		</select>
	</td></tr>
</table>
 */ ?>		
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

<br>

<table class="dataview">
	<tr>
		<th>&nbsp;</th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="110" align="left"><a href="#" class="colname" onclick="setaction('sortby_paiddate');">Tgl Bayar<?=$ctrl->sortimage('paiddate')?></a></th>
		<th width="70" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member #<?=$ctrl->sortimage('kodemember')?></a></th>
		<? /*<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_namamember');">Member Name<?=$ctrl->sortimage('namamember')?></a></th> */?>
		<th width="120" align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayarmbr');">Total MBR<?=$ctrl->sortimage('totalbayarmbr')?></a></th>
		<th width="120" align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayarbc');">Total BC<?=$ctrl->sortimage('totalbayarbc')?></a></th>
		<? /*<th width="60" align="right"><a href="#" class="colname" onclick="setaction('sortby_paymentcharge');">Payment Charge<?=$ctrl->sortimage('paymentcharge')?></a></th> */?>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesidsmi');">BC Sales Order #<?=$ctrl->sortimage('salesidsmi')?></a></th>
		<th width="120" align="right"><a href="#" class="colname" onclick="setaction('sortby_kreditbc');">Kredit BC<?=$ctrl->sortimage('kreditbc')?></a></th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_statusname');">Status<?=$ctrl->sortimage('statusname')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i%2?'<tr class="pinkrow">':'<tr>';
			echo '<td><div class="color' . $ctrl->colorstatus($item['status']) . '"></div></td>';
			echo '<td align="left"><a ' . ($i%2?'class="grid"':'') . ' href="bcvieworder.php?backpage=3&salesid=' . $item['salesid'] . '&sc=' . $ctrl->searchcriteria .'">' . $item['salesid'] . '</a></td>';
			//echo '<td align="left">' . $item['salesid'] . '</td>';
			echo '<td align="left">' . $item['paiddate']. '</td>';
			echo '<td align="left">' . htmlspecialchars($item['kodemember']). '</td>';
			//echo '<td align="left">' . htmlspecialchars($item['namamember']). '</td>';
			echo '<td align="right">' . $ctrl->valuenumber($item['totalbayarmbr']) . '</td>';
			echo '<td align="right">' . $ctrl->valuenumber($item['totalbayarbc']) . '</td>';
			//echo '<td align="right">' . $ctrl->valuenumber($item['paymentcharge']) . '</td>';
			echo '<td align="left"><a href="bcviewmyorder.php?backpage=3&purchid=' . $item['salesid'] . '&sc=' . $ctrl->searchcriteria .'">' . $item['salesidsmi'] . '</a></td>';
			//echo '<td align="left">' . htmlspecialchars($item['salesidsmi']). '</td>';			
			echo '<td align="right">' . $ctrl->valuenumber($item['kreditbc']) . '</td>';
			echo '<td align="left">' . htmlspecialchars($item['statusname']). '</td>';
			echo '</tr>';
			$i++;
		}
	}
	else
	{
		echo '<td colspan="9" align="center">no items</td>';
	}
?>
</table>
<div class="boxright" style="width:350px;padding-right:127px;padding-top:10px">
	<div class="boxcon1">
		<div class="boxleft1" style="width:240px">Total Kredit BC</div>
		<div class="boxright1" style="margin-left:20px"><?=$ctrl->valuenumber($ctrl->varvalue('totalkredit'));?></div>
	</div>
</div>
<br><br>
</div>
<?include "bcfooter.php";?>

<script language="javascript">
function validsearch()
{
	var ret = true;
	if (!isvaliddate('search_paiddate_from')) ret = checkfailed ('Date from salah format');
	if (ret && !isvaliddate('search_paiddate_to')) ret = checkfailed ('Date to salah format');	
	return ret;
}
</script>