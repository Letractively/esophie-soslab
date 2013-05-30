<?include "bcheader.php";?>
<b><br>
Selamat datang di<br>
Sophie Online Shopping<br><br>
</b>
Silahkan masukkan:<br>
<table>
	<tr><td width="110">Kode BC:</td></tr>
	<tr><td width="110"><input type="text" name="userid" id="userid" value="" size="23"/></td></tr>
	<tr><td width="110">Password:</td></tr>
	<tr><td width="110"><input type="password" name="password" id="password" value="" size="23"/></td></tr>
	<tr><td align="center"><button type="button" onclick="setaction('ok');" style="width:60px;">Login</button></td></tr>
</table>
<? if ($ctrl->varvalue("errmsg") != '') { ?>
	<div class="errmsg"><?=$ctrl->varvalue("errmsg")?></div>
<? } ?>
<br><a href="bcnewpassword.php">Lupa password</a>
<br><br>&nbsp;

<?include "bcfooter.php";?>