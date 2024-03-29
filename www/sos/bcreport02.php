<?include "bcheader.php";?>
<div class="boxcon4" >
<div class="title">Search Orders History</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">
<div class="boxcon">
	<div class="boxleft">
		<table>
			<tr><td>Online order #</td><td>
				<div class="boxstyled1" style="width:230px; float:left;">
					<input type="text" id="search_salesid" name="search_salesid" value="<?=$ctrl->value("search_salesid")?>" style="width:220px">
				</div>
			</td></tr>
			<tr><td>Tanggal <small>(dd/mm/yyyy)</small></td><td>
				<div class="boxstyled1" style="width:100px; float:left;">
					<input type="text" id="search_orderdate_from" name="search_orderdate_from" value="<?=$ctrl->value("search_orderdate_from")?>" placeholder="dd/mm/yyyy" onblur="CalendarDateLostFocus('search_orderdate_from',null);" style="width:75px"> 
					<img src="images/cal.gif" name="browsedate" id="browsedate" class="browsedate" onclick="CalendarShow('search_orderdate_from',null);">
				</div>
				<div style="float:left;padding:5px 5px 0px 5px;">to</div>
				<div class="boxstyled1" style="width:100px; float:left;">
					<input type="text" id="search_orderdate_to" name="search_orderdate_to" value="<?=$ctrl->value("search_orderdate_to")?>" placeholder="dd/mm/yyyy" onblur="CalendarDateLostFocus('search_orderdate_to',null);" style="width:75px">
					<img src="images/cal.gif" name="browsedate" id="browsedate" class="browsedate" onclick="CalendarShow('search_orderdate_to',null);">
				</div>
				
			</td></tr>
			<tr><td>Member #</td><td>
				<div class="boxstyled1" style="width:230px; float:left;">
					<input type="text" id="search_kodemember" name="search_kodemember" value="<?=$ctrl->value("search_kodemember")?>" style="width:220px">
				</div>
			</td></tr>
			<!--<tr><td>Member name</td><td><input type="text" id="search_namamember" name="search_namamember" value="<?=$ctrl->value("search_namamember")?>"></td></tr>-->
			<tr><td>Order BC #</td><td>
				<div class="boxstyled1" style="width:230px; float:left;">
					<input type="text" id="search_salesidsmi" name="search_salesidsmi" value="<?=$ctrl->value("search_salesidsmi")?>" style="width:220px">
				</div>
			</td></tr>
			<tr><td>Status</td><td>
				<div class="boxstyled1" style="width:230px; float:left;">
				<select id="search_status" name="search_status" style="width:220px">
					<option value="0,2,3,4,5,6,7,8,9,10" <?=($ctrl->value("search_status") == "0,2,3,4,5,6,7,8,9,10" ? "selected" : "")?>>All
					<option value="2" <?=($ctrl->value("search_status") == "2" ? "selected" : "")?>>New
					<option value="3,4" <?=($ctrl->value("search_status") == "3,4" ? "selected" : "")?>>In progress
					<option value="5" <?=($ctrl->value("search_status") == "5" ? "selected" : "")?>>Edited
					<option value="6,7" <?=($ctrl->value("search_status") == "6,7" ? "selected" : "")?>>Waiting payment
					<option value="8" <?=($ctrl->value("search_status") == "8" ? "selected" : "")?>>Payment accepted
					<option value="9" <?=($ctrl->value("search_status") == "9" ? "selected" : "")?>>Ready to pickup
					<option value="10" <?=($ctrl->value("search_status") == "10" ? "selected" : "")?>>Delivered
                                        <option value="0" <?=($ctrl->value("search_status") == "0" ? "selected" : "")?>>Cancelled
                                </select>
				</div>
			</td></tr>
		</table>
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
<input type="submit" onclick="if (validsearch()){setaction('search');};" class="buttongo" style="width:60px;" value="Search"/>&nbsp;&nbsp;
<input type="reset" onclick="setaction('reset');" class="buttonback" style="width:60px;" value="Clear"/>	
<br><br>
<table class="dataview" width ="955">
	<tr>
		<th>&nbsp;</th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_orderdate');">Tanggal Order<?=$ctrl->sortimage('orderdate')?></a></th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member<?=$ctrl->sortimage('kodemember')?></a></th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_namamember');">Member Name<?=$ctrl->sortimage('namamember')?></a></th>
		<th align="right"><a href="#" class="colname" onclick="setaction('sortby_totalbayar');">Total Member<?=$ctrl->sortimage('totalbayar')?></a></th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_salesidsmi');">Order BC<?=$ctrl->sortimage('salesidsmi')?></a></th>
		<th align="left"><a href="#" class="colname" onclick="setaction('sortby_statusname');">Status<?=$ctrl->sortimage('statusname')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i%2?'<tr class="pinkrow">':'<tr>';
			echo '<td width="20"><div class="color' . $ctrl->colorstatus($item['status']) . '"></div></td>';			
			echo '<td width="120" align="left"><a ' . ($i%2?'class="grid"':'') . ' href="bcvieworder.php?backpage=2&salesid=' . $item['salesid'] . '&sc=' . $ctrl->searchcriteria .'">' . $item['salesid'] . '</a></td>';
			echo '<td width="110" align="left">' . $item['orderdate']. '</td>';
			echo '<td width="60" align="left">' . htmlspecialchars($item['kodemember']). '</td>';
			echo '<td width="*" align="left">' . htmlspecialchars($item['namamember']). '</td>';
			echo '<td width="110" align="right">' . $ctrl->valuenumber($item['totalbayar']) . '</td>';
			if ( strtolower($item['salesidsmi']) != "no order" )
				echo '<td width="100" align="left"><a href="bcviewmyorder.php?backpage=2&purchid=' . $item['salesid'] .'">' . $item['salesidsmi'] . '</a></td>';
			else
				echo '<td width="100" align="left">No Order</td>';
			//echo '<td align="left">' . htmlspecialchars($item['salesidsmi']). '</td>';
			echo '<td width="120" align="left">' . htmlspecialchars($item['statusname']). '</td>';
			echo '</tr>';
			$i++;
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