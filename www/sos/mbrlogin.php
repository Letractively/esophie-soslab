<? 
    $footer_params = "nolinks"; 
    include "mbrheader.php";
?>
<br/>Silahkan masukan kode member dan nomor rekening Anda:<br/><br/>
<div style="width:160px;text-align:left;">
    <div>Kode Member :</div>    
    <div class="boxstyled1" style="width:150px;"><input placeholder="Kode member" type="text" name="username" id="username" value="" style='width:135px; -wap-input-format: "*N"'/></div>
    <div>Nomor Rekening :</div>
    <div class="boxstyled1" style="width:150px;"><input placeholder="Nomor rekening" type="text" name="norekening" id="norekening" value="" style='width:135px; -wap-input-format: "*N"'/></div>
</div>

<br/><?= $ctrl->printerrors(); ?>

<input type="hidden" name="redirect" value="login" />
<button type="submit" onclick="setaction('save');" style="width:60px;">OK</button>
<?include "mbrfooter.php";?>
