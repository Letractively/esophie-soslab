<?include "mbrheader.php";?>
	<div class="boxfont2">Silahkan <font class="pink">pilih items</font> dari katalog Sophie:</div>
	<? for ($i=1;$i<=$ctrl->maxitem;$i++) { ?>
		<? if ($ctrl->value("item".$i."err") != '') { ?>
			<div class="boxerr1"><?=$ctrl->value("item".$i."err")?></div>
		<? } ?>
		<div>
			<div class="boxstyled1" style="width:248px; float:left;"><input type="textbox" name="item<?=$i?>" id="item<?=$i?>" value="<?=$ctrl->value("item".$i)?>" maxlength="10" style="width:230px" placeholder="Kode Item"></div>
			<div class="boxstyled1" style="width:60px; float:left; margin-left:10px;"><input type="textbox" name="item<?=$i?>qty" id="item<?=$i?>qty" value="<?=$ctrl->value("item".$i."qty")?>" maxlength="3" style="width:50px" placeholder="Jumlah"></div>
		</div>
	<? } ?>	
	<!--<button type="button" onclick="setaction('back');" style="width:60px;">Back</button>-->
	<button type="reset" style="width:60px;" onclick="setaction('reset');">Reset</button>&nbsp;<button type="button" onclick="setaction('save');" style="width:60px;">OK</button>	
<?include "mbrfooter.php";?>