<?include "mbrheader.php";?>
<center>
	<div class="boxfont2">Pilih salah satu <font class="pink">cara pembayaran</font>:</div>
	<div class="boxcon">
		<?
			if (is_array($ctrl->items))
			{
				$i = 0;
				foreach ($ctrl->items as $item)
				{
					if ( $item['mobilenumber'] == '0' )
					{
						?>
							<input type="radio" id="mop" name="mop" value="<?=$item['paymentmode']?>" onclick="opendescription(<?=$i?>)"><?=$item['name']?><br>
							<div id="description" name="description" class="boxcon2">
							<?=htmlspecialchars($item['description'])?>
							</div>
						<?
					}
					else
					{
						?>
							<input type="radio" id="mop" name="mop" value="<?=$item['paymentmode']?>" onclick="opendescription(<?=$i?>)"><?=$item['name']?><br>
							<div id="description" name="description" class="boxcon2">
							<?=htmlspecialchars($item['description']) ?>
							<? echo "<br> Mobile number : <input type='text' id='mobilenumber' name='mobilenumber'></input> ex: 08111234567"; ?>
							</div>
						<?
					}
					$i++;
				}
			}
		?>
	</div>
	<br>
	<button type="button" onclick="setaction('confirm');" style="width:80px">OK</button>
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
</script>