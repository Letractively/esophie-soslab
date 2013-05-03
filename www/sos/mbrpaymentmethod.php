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
							<div class="boxleft" style="width:200px">
								<input type="radio" id="mop" name="mop" value="<?=$item['paymentmode']?>" onclick="opendescription(<?=$i?>)" <?=($ctrl->selectedpaymentmode == $item['paymentmode']?'checked':'')?>><?=$item['name']?><br>
							</div>
							<div class="boxright" style="width:90px;text-align:right;margin-right:10px;">
								+<?=$ctrl->valuenumber($item['totalfee'])?>
							</div>
						</div>							
						<div id="description" name="description" class="boxcon2">
						<?=$item['fee']?>
						<?=($item['mobilenumber'] != '0' ? '<br> Mobile number : <input type="text" id="mobilenumber" name="mobilenumber"></input> ex: 08111234567' : '')?>
						<br><a href="#<?=$item['paymentmode']?>">Payment instructions...</a>
						</div>
					<?
					$i++;
				}
			}
		?>
	</div>
	<div class="boxcon3" style="text-align:left;padding: 10px 10px 10px 10px;">
		<?
			if (is_array($ctrl->items))
			{
				$i = 0;
				foreach ($ctrl->items as $item)
				{
					echo $i ? '<br><br>' : '';
					echo '<a href="'. $item['paymentmode'] . '"></a>';
					echo $item['name'] . ': ' . $item['description'];
					$i++;
				}
			}
		?>
	</div>
	<br>
	<button type="button" onclick="setaction('back');" class="back">Kembali</button>
	<button type="button" onclick="setaction('confirm');" style="width:80px">Validasi</button>
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