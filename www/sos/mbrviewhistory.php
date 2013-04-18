<?include "mbrheader.php";?>
	<div class="boxcon3">
		<div class="boxleft3">
			<?if ($ctrl->mbrmsg['color'] != '') {?>
			<div class="color<?=$ctrl->mbrmsg['color']?>"></div>
			<?}?>
		</div>
		<div class="boxright3"><b><?=$ctrl->mbrmsg['title']?></b><br><?=$ctrl->mbrmsg['body']?></div>
		<? if (isset($ctrl->mbrmsg['link1'])) { ?>
		<div class="boxcon3-1">
			<?if (isset($ctrl->mbrmsg['link1'])) {?><a href="<?=$ctrl->mbrmsg['link1']?>"><?=$ctrl->mbrmsg['link1label']?></a><?}?>
			<?if (isset($ctrl->mbrmsg['link2'])) {?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$ctrl->mbrmsg['link2']?>"><?=$ctrl->mbrmsg['link2label']?></a><?}?>
		</div>
		<? } ?>
	</div>
	<br>
	<div class="boxcon">
		<table class="dataview" width="340">
			<tr>
				<th>&nbsp;</th>
				<th>Order #</th>
				<th>Date Time</th>
				<th>BC</th>
				<th align="right">Total</th>
				<th>Status</th>
			</tr>
			<?
				if (is_array($ctrl->orderhistory))
				{
					$i = 0;
					foreach ($ctrl->orderhistory as $orders)
					{						
						echo $i++%2?'<tr class="pinkrow" ':'<tr ';
						echo ' style="cursor:pointer" ';
						echo ' onclick="gotopage(\'mbrvieworder.php\',\'edit=1;salesid=' . $orders['salesid'] . '\')">';
						?>
							<td><div class="color<?=$ctrl->colorstatus($orders['status'])?>"></div></td>
							<td><?=$orders['salesid']?></td>
							<td><?=$orders['orderdate']?></td>
							<td><?=$orders['bcid']?></td>
							<td align="right"><?=$ctrl->valuenumber($orders['total'])?></td>
							<td><?=$orders['userstatus']?></td>
						</tr>
						<?
					}
				}
				else
				{
					echo '<td colspan="6" align="center">no orders</td>';
				}
			?>
			
		</table>
	</div>
	<div class="boxcon4">
		<table>
			<tr>
				<td width="12px"><div class="color01"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['openorder'])?></td>
				<td width="12px"><div class="color02"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ordered'])?></td>
				<td width="12px"><div class="color05"></div></td><td width="50"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['edited'])?></td>
				<td width="12px"><div class="color06"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['validated'])?></td>
			</tr>
			<tr>
				<td><div class="color08"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['paid'])?></td>
				<td><div class="color09"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ready'])?></td>
				<td><div class="color10"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['delivered'])?></td>
				<td><div class="color00"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['cancelled'])?></td>
			</tr>		
		</table>
	</div>
<?include "mbrfooter.php";?>