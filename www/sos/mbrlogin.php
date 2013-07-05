<? 
    $footer_params = "nolinks"; 
    include "mbrheader.php";
?>
<br/>Silahkan masukan kode member dan nomor rekening Anda:<br/><br/>
<div>
        <div class="boxstyled1" style='width:250px;' onclick="document.getElementById('username').focus(); return false;"><div style='width:90px;'>Kode Member :</div><input placeholder="Ex: 5000752130" type="text" name="username" id="username" value="" style='width:150px; -wap-input-format: "*N"'/></div>
        <div class="boxstyled1" style='width:250px;' onclick="document.getElementById('norekening').focus(); return false;"><div style='width:90px;'>Nomor Rekening :</div><input placeholder="Ex: 8004621385" type="text" name="norekening" id="norekening" value="" style='width:150px; -wap-input-format: "*N"'/></div>
</div>

<br/><?= $ctrl->printerrors(); ?>

<input type="hidden" name="redirect" value="login" />
<button type="submit" onclick="setaction('save');" style="width:60px;">OK</button>
<?include "mbrfooter.php";?>
