<?include "bcheaderright.php";?>
<div class="boxcon" style="width:510px;padding-left:100px">
	<? if ($ctrl->status == $ctrl->sysparam['salesstatus']['inprogress']) { ?>	
		<div class="title">Order InProses</div>
		Silahkan tunggu, atau tekan tombol refresh.
	<? } ?>
	<? if ($ctrl->status != $ctrl->sysparam['salesstatus']['inprogress']) { ?>	
		<div class="title">Order Confimed</div>
		<b>Order #<?=$ctrl->value('salesid')?> baru di konfirmasikan.</b>
		<br><br>Email & SMS otomatis akan dikirim ke member untuk lanjut ke pembayaran.
	<? } ?>
	<br><br>
	<input type="hidden" name="salesid" id ="salesid" value="<?=$ctrl->value('salesid')?>">
	<div class="boxcon">
		<table width="280">
			<tr><td width="150"><b>Order#</b></td><td align="right"><?=$ctrl->value('salesid')?></td></tr>
			<tr><td><b>Status</b></td><td align="right"><?=$ctrl->varvalue('userstatus')?></td></tr>
			<tr><td><b>Tanggal</b></td><td align="right"><?=$ctrl->varvalue('orderdate')?></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td><b>Member ID</b></td><td align="right"><?=$ctrl->varvalue('mbrno')?></td></tr>
			<tr><td><b>Member Name</b></td><td align="right"><?=$ctrl->varvalue('mbrname')?></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td><b>Total Bayar Member</b></td><td align="right"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayaredited'));?></td></tr>
			<tr><td><b>Total Order Tambahan</b></td><td align="right"><?=$ctrl->valuenumber($ctrl->varvalue('totalbayarbc'));?></td></tr>
		</table>
	</div>
	<? if ($ctrl->status == $ctrl->sysparam['salesstatus']['inprogress']) { ?>
		<button type="submit" style="width:80px;">Refresh</button>
	<? } ?>
</div>
<div style="width:703px;text-align:right">	
	<button type="button" onclick="setaction('cancel');" style="width:80px;">Close</button>
</div>			

<?include "bcfooterright.php";?>

<? if ($ctrl->status == $ctrl->sysparam['salesstatus']['inprogress']) { ?>	
<script type="text/javascript">
    window.onload = setupRefresh;

    function setupRefresh() {
      setTimeout("refreshPage();", 5000); // milliseconds
    }
    function refreshPage() {
       frmmain.submit();
    }
</script>
<? } ?>