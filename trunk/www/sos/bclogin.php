<?include "bcheader.php";?>
<br/><b>Backoffice BC</b>
<br/><br/>
<table style="width:230px; text-align:right;">
    <tr><td>Kode BC:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="text" name="userid" id="userid" value="" placeholder="Kode BC" style="width:150px"/></div></td></tr>
    <tr><td>Password:</td>
    <td><div class="boxstyled1" style="width:100%"><input type="password" name="password" id="password" value="" placeholder="BC password" style="width:150px"/></div></td></tr>
</table>
<br/>
<? if ($ctrl->varvalue("errmsg") != '') { ?>
	<div class="errormessage"><?=$ctrl->varvalue("errmsg")?></div>
<? } ?>
<input type="submit" onclick="setaction('ok');" class="buttongo" value="Login" />
<br/><br/><a href="bcnewpassword.php">Lupa password</a><br/><br/>

<?include "bcfooter.php";?>