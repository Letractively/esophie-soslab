<?include "bcheader.php";?>

<div class="boxcon" style="margin:10px; padding:10px; text-align:center;">
<?if ($ctrl->pageindex == 1) { ?>
Silahkan merubah password Anda di sini:
<br/><br/>
<table style="width:100%; text-align:right;">
    <tr><td>Kode BC:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="text" name="userid" id="userid" value="<?=$ctrl->value("userid")?>" placeholder="Kode BC" style="width:150px"/></div></td></tr>
    <tr><td>Email:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="email" name="email" id="email" value="<?=$ctrl->value("email")?>" placeholder="Email address" style="width:150px"/></div></td></tr>
    <tr><td>Password lama:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="password" name="oldpwd" id="oldpwd" value="<?=$ctrl->value("oldpwd")?>" placeholder="Old password" style="width:150px"/></div></td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>Password baru x1:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="password" name="newpwd1" id="newpwd1" value="" placeholder="New password" style="width:150px"/></div></td></tr>
    <tr><td>Password baru x2:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="password" name="newpwd2" id="newpwd2" value="" placeholder="New password (again)" style="width:150px"/></div></td></tr>

</table>
<br/>
<input type="submit" onclick="setaction('ok');" class="buttongo" style="width:180px;" value="Ganti password" />
<br/><br/>
<? if ($ctrl->varvalue("errmsg") != '') { ?>
        <div class="errormessage"><?=$ctrl->varvalue("errmsg")?></div>
<? } ?>
<a href="bclogin.php"><< Kembali ke layar login</a>
<? } else { ?>
	<br>Password Anda sudah dirubah dan dikirimkan ke alamat email Anda.<br><br>
	Silahkan <a href="bclogin.php"> kembali ke layar login</a><br><br>
<? } ?>
</div>
<?include "bcfooter.php";?>