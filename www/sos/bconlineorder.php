<?include "bcheaderleft.php";?>
<div class="title">New Orders</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="right">Sisa Waktu</th>
	</tr>
	<?		
		if (is_array($ctrl->neworders)) 
		{
			$i=0;
			foreach ($ctrl->neworders as $orders)
			{
				echo $i++%2?"<tr class=\"pink\">":"<tr>";
				echo "<td align=\"center\"><a href=\"bcvieworder.php?salesid=" . urlencode($orders['salesid']) . "\"><img src=\"images/search.png\"/></a></td>";
				echo "<td align=\"left\">". $orders['salesid'] . "</td>";				
				echo "<td align=\"left\">" . $orders['orderdate'] . "</td>";
				echo "<td align=\"left\">" . $orders['kodemember'] . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($orders['totalbayar']) . "</td>";
				echo "<td align=\"right\">" . $orders['timeleft'] . "</td>";
				echo "</tr>";
			}
		} else {
			echo '<td colspan="6" align="center">no new order</td>';
		}
	?>
</table>

<br>
<div class="title">Orders In Progress</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="center">Status</th>
	</tr>
	<?		
		if (is_array($ctrl->inprogressorders)) 
		{
			$i=0;
			foreach ($ctrl->inprogressorders as $orders)
			{
				echo $i++%2?"<tr class=\"pink\">":"<tr>";
				echo "<td align=\"center\"><a href=\"bcvieworder.php?salesid=" . urlencode($orders['salesid']) . "\"><img src=\"images/search.png\"/></a></td>";
				echo "<td align=\"left\">". $orders['salesid'] . "</td>";				
				echo "<td align=\"left\">" . $orders['orderdate'] . "</td>";
				echo "<td align=\"left\">" . $orders['kodemember'] . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($orders['totalbayar']) . "</td>";
				if ( $orders['statuscode'] == 1 )
					echo "<td align=\"center\" class=\"red\">" . $orders['syncstatus'] . "</td>";
				else
					echo "<td align=\"center\" class=\"green\">" . $orders['syncstatus'] . "</td>";
				echo "</tr>";
			}
		} else {
			echo '<td colspan="6" align="center">no order in progress</td>';
		}
	?>
</table>

<br>
<div class="title">Validated Orders</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="center">Status</th>
	</tr>
	<?	
		if (is_array($ctrl->pendingorders)) 
		{
			$i=0;
			foreach ($ctrl->pendingorders as $orders)
			{
				echo $i++%2?"<tr class=\"pink\">":"<tr>";
				echo "<td align=\"center\"><a href=\"bcvieworder.php?salesid=" . urlencode($orders['salesid']) . "\"><img src=\"images/search.png\"/></a></td>";
				echo "<td align=\"left\">". $orders['salesid'] . "</td>";				
				echo "<td align=\"left\">" . $orders['orderdate'] . "</td>";
				echo "<td align=\"left\">" . $orders['kodemember'] . "</td>";
				echo "<td align=\"right\">" . $ctrl->valuenumber($orders['totalbayar']) . "</td>";
				if ( $orders['statuscode'] == 0 )
					echo "<td align=\"center\" class=\"red\">" . $orders['userstatus'] . "</td>";
				else if ( $orders['statuscode'] == 6 )
					echo "<td align=\"center\" class=\"orange\">" . $orders['userstatus'] . "</td>";
				else
					echo "<td align=\"center\" class=\"green\">" . $orders['userstatus'] . "</td>";
					
				echo "</tr>";
			}
		} else {
			echo '<td colspan="6" align="center">no pending order</td>';
		}
	?>
</table>



<?include "bcfooterleft.php";?>