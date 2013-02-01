<?include "mbrheader.php";?>
	<p class="pink">Selamat datang <?=$ctrl->mbrname?> !</p>
	<p align="left">Dibawah ini adalah list 3 online orders terakhir yang sudah pernah dilakukan di Sophie Online Shopping:</p>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th>Order #</th>
				<th>Date Time</th>
				<th>BC</th>
				<th align="right">Total</th>
				<th>Status</th>
			</tr>
			<?
				if (is_array($ctrl->orderhistory))
				{
					$i=0;
					foreach ($ctrl->orderhistory as $orders)
					{						
						echo $i++%2?"<tr class=\"pinkrow\">":"<tr>";
						?>
							<td><?=$orders['salesid']?></td>
							<td><?=$orders['orderdate']?></td>
							<td><?=$orders['bcid']?></td>
							<td align="right"><?=$ctrl->valuenumber($orders['total'])?></td>
							<td><?=$orders['status']?></td>
						</tr>
						<?
						
					}
				}
				else
				{
					echo '<td colspan="5" align="center">no orders</td>';
				}
			?>
		</table>
	</div>
	<p>Untuk belanja lagi di Sophie Online Shopping, klik New Order.</p>
	<button type="button" onclick="setaction('memberinfo');">New order</button>	
<?include "mbrfooter.php";?>