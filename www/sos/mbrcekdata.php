<?include "mbrheader.php";?>
<div class="boxfont2">Silahkan cek <font class="pink">data member anda</font>:</div>
<div class="boxstyled1"><div>Member #</div><input type="text" value="<?=$ctrl->value("memberno")?>" disabled><input type="hidden" name="memberno" id="memberno" maxlength="20" value="<?=$ctrl->value("memberno")?>"></div>
<div class="boxstyled1"><div>Nama</div><input type="textbox" name="nama" id="nama" maxlength="50" value="<?=$ctrl->value("nama")?>"></div>
<div class="boxstyled1" style="height:68px"><div>Alamat</div><textarea name="alamat" id="alamat" rows="4"><?=$ctrl->value("alamat")?></textarea></div>
<div class="boxstyled1"><div>Handphone #</div><input type="textbox" name="handphone" id="handphone" maxlength="50" value="<?=$ctrl->value("handphone")?>"></div>
<div class="boxstyled1"><div>Email</div><input type="textbox" name="email" id="email" maxlength="80" value="<?=$ctrl->value("email")?>"></div>
<? if ( $ctrl->value("errmsg") != "" ) echo '<div class="boxerr1">' . $ctrl->value("errmsg") . '</div>' ?>
<button type="button" onclick="setaction('ok');" style="width:60px;">OK</button>
<?include "mbrfooter.php";?>