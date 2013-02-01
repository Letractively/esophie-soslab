<?include "bcheader.php";?>
<div class="boxcon4">
<div class="title">BC Orders report</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">

<table>
	<tr><td>Online order #</td><td><input type="text" id="search_salesid" name="search_salesid" value="<?=$ctrl->value("search_salesid")?>"></td></tr>
	<tr><td>Date <small>(dd/mm/yyyy)</small></td><td><input type="text" id="search_orderdate_from" name="search_orderdate_from" value="<?=$ctrl->value("search_orderdate_from")?>" placeholder="dd/mm/yyyy"> to <input type="text" id="search_orderdate_to" name="search_orderdate_to" value="<?=$ctrl->value("search_orderdate_to")?>" placeholder="dd/mm/yyyy"></td></tr>
	<!--<tr><td>Member #</td><td><input type="text" id="search_kodemember" name="search_kodemember" value="<?=$ctrl->value("search_kodemember")?>"></td></tr>-->
	<!--<tr><td>Member name</td><td><input type="text" id="search_namamember" name="search_namamember" value="<?=$ctrl->value("search_namamember")?>"></td></tr>-->
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
<br>
<button type="button" onclick="if (validsearch()){setaction('search');};" style="width:60px;">Search</button>&nbsp;&nbsp;
<button type="button" onclick="setaction('reset');" style="width:60px;">Clear</button>	
<br><br>
<table class="dataview">
	<tr>
		<th width="90" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Online Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="110" align="left"><a href="#" class="colname" onclick="setaction('sortby_orderdate');">Date/Time<?=$ctrl->sortimage('orderdate')?></a></th>
		<th width="70" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member #<?=$ctrl->sortimage('kodemember')?></a></th>
		<!--<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_namamember');">Member Name<?=$ctrl->sortimage('namamember')?></a></th>-->
		<th width="120" align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayarmbr');">Total Bayar Member<?=$ctrl->sortimage('totalbayarmbr')?></a></th>
		<th width="60" align="right"><a href="#" class="colname" onclick="setaction('sortby_paymentcharge');">Payment Charge<?=$ctrl->sortimage('paymentcharge')?></a></th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesidsmi');">BC Sales Order #<?=$ctrl->sortimage('salesidsmi')?></a></th>
		<th width="90" align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayarbc');">Total Bayar BC<?=$ctrl->sortimage('totalbayarbc')?></a></th>
		<th width="90" align="right"><a href="#" class="colname" onclick="setaction('sortby_kreditbc');">Kredit BC<?=$ctrl->sortimage('kreditbc')?></a></th>
		<th width="100" align="left"><a href="#" class="colname" onclick="setaction('sortby_statusname');">Status<?=$ctrl->sortimage('statusname')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
			echo "<td align=\"left\">" . $item['salesid'] . "</td>";
			echo "<td align=\"left\">" . $item['orderdate']. "</td>";
			echo "<td align=\"left\">" . htmlspecialchars($item['kodemember']). "</td>";
			//echo "<td align=\"left\">" . htmlspecialchars($item['namamember']). "</td>";
			echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalbayarmbr']) . "</td>";
			echo "<td align=\"right\">" . $ctrl->valuenumber($item['paymentcharge']) . "</td>";
			echo "<td align=\"left\">" . htmlspecialchars($item['salesidsmi']). "</td>";
			echo "<td align=\"right\">" . $ctrl->valuenumber($item['totalbayarbc']) . "</td>";
			echo "<td align=\"right\">" . $ctrl->valuenumber($item['kreditbc']) . "</td>";
			echo "<td align=\"left\">" . htmlspecialchars($item['statusname']). "</td>";
			echo "</tr>";
		}
	}
	else
	{
		echo '<td colspan="9" align="center">no items</td>';
	}
?>
</table>
</div>
<?include "bcfooterleft.php";?>

<script language="javascript">
function validsearch()
{
	var ret = true;
	if (!isvaliddate('search_orderdate_from')) ret = checkfailed ('Date from salah format');
	if (ret && !isvaliddate('search_orderdate_to')) ret = checkfailed ('Date to salah format');	
	return ret;
}
</script>