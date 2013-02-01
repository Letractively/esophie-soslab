<?include "bcheader.php";?>

<br>
<?if ($ctrl->pageindex == 1) { ?>
	Silahkan masukkan:
	<table>
		<tr><td width="110">Kode BC:</td></tr>
		<tr><td width="110"><input type="text" name="userid" id="userid" value="<?=$ctrl->value("userid")?>" size="23"/></td></tr>
		<tr><td width="110">Email:</td></tr>
		<tr><td width="110"><input type="email" name="email" id="email" value="<?=$ctrl->value("email")?>" size="23"/></td></tr>
		<tr><td align="center"><input type="button" value="Kirim password baru" onclick="setaction('ok');"></td></tr>
		
	</table>
	<? if ($ctrl->varvalue("errmsg") != '') { ?>
		<div class="errmsg"><?=$ctrl->varvalue("errmsg")?></div>
	<? } ?>
<? } else { ?>
	Password baru sudah dikirimkan ke email anda.<br>
	silahkan cek email anda beberapa menit kedepan.<br>
<? } ?>
<br><a href="index.php">kembali ke layar login</a>
<br><br>&nbsp;
<?include "bcfooter.php";?>