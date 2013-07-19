<?include "mbrheader.php";?>
<center>
	<div class="boxcon5">Pilih salah satu <font class="pink">cara pembayaran</font>:</div>
	<?=$ctrl->printerrors()?>
	<div class="boxcon">
		<?
			if (is_array($ctrl->items))
			{
				$i = 0;
				foreach ($ctrl->items as $item)
				{
					?>
						<div class="boxcon">
							<div class="boxleft" style="width:150px">
								<input type="radio" id="mop" name="mop" value="<?=$item['paymentmode']?>" onclick="opendescription(<?=$i?>)" <?=($ctrl->selectedpaymentmode == $item['paymentmode']?'checked':'')?>><?=$item['name']?><br>
							</div>
							<div class="boxright" style="width:140px;text-align:right;margin-right:10px;">
                                                            <img style="float:right;margin-left:15px;" src="images/logo-payment-<?= strtolower($item['paymentmode'])?>.png"/>
                                                            <?  if ($item['totalfee'] == 0) echo 'gratis';
                                                                else echo '+' . $ctrl->valuenumber($item['totalfee']);
                                                            ?>
							</div>
						</div>							
						<div id="description" name="description" class="boxcon2">
						<?=$item['fee']?>
						<?=($item['mobilenumber'] != '0' ? '<br> Mobile number : <input type="text" id="mobilenumber" name="mobilenumber"></input> ex: 08111234567' : '')?>
						<br><br><? include 'include/paymode_' . strtolower($item['paymentmode']) . '.php'; ?></a>
						</div>
					<?
					$i++;
				}
			}
		?>
	</div>
		
	<table border="0" width="100%">
		<tr>
		<td>
			<input type="button" class="buttonback" onclick="setaction('back');" value="&lt;&lt; Kembali"/>
		</td>
		<td align="right">
			<input type="button" class="buttongo" onclick="setaction('confirm');" value="Validasi &gt;&gt" />
		</td>
		</tr>
	</table>

</center>
<?include "mbrfooter.php";?>

<script language="javascript">	
	function opendescription(idx)
	{	
		var obj = document.getElementsByName('description');
		var i = 0;
		for (i = 0; i<obj.length;i++)
		{
			obj[i].style.display = "none";
		}
		
		if(arguments.length)
			obj[idx].style.display = "block";
		
	}
	
	checkselected();
	function checkselected()
	{
		var obj = document.getElementsByName('mop');
		var i = 0;		
		for (i = 0; i<obj.length;i++)
		{
			if (obj[i].checked)
				opendescription(i);
		}
	}
</script>