<? 
    $footer_params = "nolinks"; 
    include "mbrheader.php";
?>
<br/><em>Silahkan masukan kode member<br/>dan nomor rekening Anda:</em><br/><br/>
<table style="text-align: right;">
    <tr><td>Kode Member :</td>
    <td><div class="boxstyled1" style="width:160px"><input placeholder="Kode member" type="text" name="username" id="username" value="" style="width:150px"/></div></td></tr>
    <tr><td>Nomor Rekening :</td>
    <td><div class="boxstyled1" style="width:160px"><input placeholder="Nomor rekening" type="text" name="norekening" id="norekening" value="" style="width:150px"/></div></td></tr>
</table>

<br/><?= $ctrl->printerrors(); ?>

<input type="hidden" name="redirect" value="login" />
<input type="submit" class="buttongo" onclick="setaction('save');" value="OK"/>
<?include "mbrfooter.php";?>
