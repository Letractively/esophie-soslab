<?include "mbrheader.php";?>
	<p>Selamat datang di website Sophie Online Shopping.<br>
	Sebelum anda bisa melakukan order online, <br>
	Persyaratan dibawah ini harus anda setujui terlebih dahulu:</p>
	
	<p><?=$ctrl->varvalue("disclaimer")?></p>
	
	<?if($ctrl->firstlogin) { ?>
		<input type="checkbox" name="agree" id="agree" value="1"> Saya setuju dengan persyaratan diatas.
		<? if($ctrl->varvalue("errmsg") != '') { ?><br><span class="errmsg"><?=$ctrl->varvalue("errmsg")?></span><?}?>
		<br><button type="button" onclick="setaction('setuju');">Setuju</button>
	<? } else { ?>
		<button type="button" onclick="setaction('lanjut');">Lanjut</button>
	<? } ?>
	
	
<?include "mbrfooter.php";?>