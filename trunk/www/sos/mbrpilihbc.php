<?include "mbrheader.php";?>
	<div class="boxfont2">Silahkan <font class="pink">pilih salah satu BC</font> di daerah anda:</div>
	<div class="boxstyled1"><div>BC</div><select name="bc" id="bc" onchange="setaction('refreshbc')"><? $ctrl->getbc(); ?></select></div>
	<div class="boxcon" style="padding-left:7px;">
		<input type="checkbox" name="defaultbc" id="defaultbc" value="1" <?if ($ctrl->value('defaultbc') == '1') echo 'checked';?>> set sebagai <font class="pink">default BC</font>.
	</div>
	<div class="boxcon" style="padding-left:7px;">
		<div class="boxcon1"><?=$ctrl->varvalue('bcno');?></b> - <?=$ctrl->varvalue('bcname');?></div>
		<div class="boxcon1"><?=$ctrl->varvalue('bcaddress');?></div>
		<div class="boxcon1">TEL: <?=$ctrl->varvalue('bcphone');?></div>
	</div>
	<button type="button" onclick="setaction('save');" style="width:60px;">OK</button>		
<?include "mbrfooter.php";?>