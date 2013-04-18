<?include "bcheaderleft.php";?>
<div class="title">My Orders</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="100">Online Order #</th>
		<th width="80">SMI Order #</th>
		<th width="110">Date / Time</th>
		<th width="80">Total</th>
		<th width="120">Status</th>
	</tr>
	<?		
		if (is_array($ctrl->orders)) {
			foreach ($ctrl->orders as $orders)
			{
				echo "<tr>";
				echo "<td><a href=\"bcviewmyorder.php?purchid=" . urlencode($orders['purchid']) . "\"><img src=\"images/search.png\"/></a></td>";				
				echo "<td align=\"left\">". $orders['purchid'] . "</td>";	
				echo "<td>" . $orders['salesidsmi'] . "</td>";
				echo "<td>" . $orders['orderdate'] . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($orders['totalbayar']) . "</td>";
				echo "<td>" . $orders['userstatus'] . "</td>";
				echo "</tr>";
			}
		} else {
			echo '<td colspan="5" align="center">no order</td>';
		}
	?>
</table>
<?include "bcfooterleft.php";?>