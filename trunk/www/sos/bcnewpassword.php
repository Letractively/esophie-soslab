<?include "bcheader.php";?>

<div class="boxcon" style="margin:10px; padding:10px; text-align:center;">
<?if ($ctrl->pageindex == 1) { ?>
Silahkan masukkan:
<br/><br/>
<table style="width:100%; text-align:right;">
    <tr><td>Kode BC:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="text" name="userid" id="userid" value="" placeholder="Kode BC" style="width:150px"/></div></td></tr>
    <tr><td>Email:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="email" name="email" id="email" value="<?=$ctrl->value("email")?>" placeholder="Email address" style="width:150px"/></div></td></tr>
</table>
<br/>
<input type="submit" onclick="setaction('ok');" class="buttongo" style="width:180px;" value="Kirim password baru" />
<br/><br/>
<? if ($ctrl->varvalue("errmsg") != '') { ?>
        <div class="errormessage"><?=$ctrl->varvalue("errmsg")?></div>
<? } ?>
<a href="bclogin.php"><< Kembali ke layar login</a>
<? } else { ?>
	<br>Password baru sudah dikirimkan ke email Anda.<br><br>
	Silahkan cek email anda beberapa menit kedepan.<br><br>
<? } ?>
</div>
<?include "bcfooter.php";?>