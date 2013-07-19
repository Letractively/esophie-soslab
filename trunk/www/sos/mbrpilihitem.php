<?include "mbrheader.php";?>
	<br/><div class="boxfont2">Silahkan <font class="pink">pilih items</font> dari katalog Sophie:</div>
        <table>
        <tr>
                <th style="text-align:left;border-bottom: 1px solid #d1d1d1; color:#d1d1d1; padding: 5px 10px;">Kode item</th>
                <th style="text-align:left;border-bottom: 1px solid #d1d1d1; color:#d1d1d1; padding: 5px 10px;">Jumlah</th>
        </tr>
	<? for ($i=1;$i<=$ctrl->maxitem;$i++) { ?>
		<? if ($ctrl->value("item".$i."err") != '') { ?>
			<tr><td colspan="2"><div class="boxerr1" style="text-align:left;"><?=$ctrl->value("item".$i."err")?></div></td></tr>
		<? } ?>
                <tr>
                    <td><div class="boxstyled1" style="width:248px;margin:2px"><input type="textbox" name="item<?=$i?>" id="item<?=$i?>" value="<?=$ctrl->value("item".$i)?>" maxlength="10" style="width:230px" placeholder="Kode Item"></div></td>
                    <td><div class="boxstyled1" style="width:60px; margin:2px"><input type="textbox" name="item<?=$i?>qty" id="item<?=$i?>qty" value="<?=$ctrl->value("item".$i."qty")?>" maxlength="3" style="width:50px" placeholder="Jumlah"></div></td>
                </tr>
	<? } ?>	
        </table><br/>
	<input type="reset" onclick="setaction('reset');" class="buttonback" value="Reset" style="width:80px;"/>&nbsp;
        <input type="submit" onclick="setaction('save');" class="buttongo" value="OK" style="width:80px;"/>	
<?include "mbrfooter.php";?>