<? 
    $footer_params = "nolinks"; 
    include "mbrheader.php";
?>
<br/>Silahkan masukan kode member dan nomor rekening Anda:<br/><br/>
<div>
        <div class="boxstyled1" style="width:250px;"><input placeholder="Kode Member" type="text" name="username" value="" style='width:240px; -wap-input-format: "*N"'/></div>
        <div class="boxstyled1" style="width:250px;"><input placeholder="Nomor Rekening" type="text" name="norekening" value="" style='width:240px; -wap-input-format: "*N"'/></div>
</div>

<br/><?= $ctrl->printerrors(); ?>

<input type="hidden" name="redirect" value="login" />
<button type="button" onclick="setaction('save');" style="width:60px;">OK</button>
<?include "mbrfooter.php";?>
