<?include "bcheaderleft.php";?>
<div class="title">Stock BC On Hold</div>
<input type="hidden" id="sortby" name="sortby" value="<?=$ctrl->sortby?>">
<input type="hidden" id="sortorder" name="sortorder" value="<?=$ctrl->sortorder?>">
<table class="dataview">
	<tr>
		<th width="50" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemid');">Item ID<?=$ctrl->sortimage('itemid')?></a></th>
		<th width="150" align="left"><a href="#" class="colname" onclick="setaction('sortby_itemname');">Item Name<?=$ctrl->sortimage('itemname')?></a></th>
		<th width="80" align="right"><a href="#" class="colname" onclick="setaction('sortby_qtybc');">Qty<?=$ctrl->sortimage('qtybc')?></a></th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_salesid');">Online Order #<?=$ctrl->sortimage('salesid')?></a></th>
		<th width="60" align="left"><a href="#" class="colname" onclick="setaction('sortby_kodemember');">Member<?=$ctrl->sortimage('kodemember')?></a></th>
		<th width="120" align="left"><a href="#" class="colname" onclick="setaction('sortby_status');">Status<?=$ctrl->sortimage('status')?></a></th>
	</tr>
	<?
	if (is_array($ctrl->items))
	{
		$i=0;
		foreach ($ctrl->items as $item)
		{
			echo $i++%2?'<tr class="pinkrow">':'<tr>';
			echo '<td align="left">' . $item['itemid'] . '</td>';
			echo '<td align="left">' . htmlspecialchars($item['itemname']). '</td>';
			echo '<td align="right">' . $ctrl->valuenumber($item['qtybc']) . '</td>';
			echo '<td align="left"><a href="bcvieworder.php?salesid=' . urlencode($item['salesid']) . '">'. $item['salesid'] . '</a></td>';	
			echo '<td align="left"> #' . htmlspecialchars($item['kodemember']). ' ' . $item['namamember'] .'</td>';
			echo '<td align="left">' . htmlspecialchars($item['status']). '</td>';
			echo '</tr>';
		}
	}
	else
	{
		echo '<td colspan="6" align="center">no items on hold</td>';
	}
?>
</table>
<?include "bcfooterleft.php";?>