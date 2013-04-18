<?include "bcheaderleft.php";?>
<div class="boxcon5" style="width:660px">
	<center>
	<table width="100%">
		<tr>
			<td width="12px"><div class="color01"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ordered'])?> (<?=$ctrl->statuscount['orderbaru'] ?>)</td>
			<td width="12px"><div class="color02"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['bypassed'])?> (<?=$ctrl->statuscount['dalamproses'] ?>)</td>
			<td width="12px"><div class="color05"></div></td><td width="50"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['edited'])?> (<?=$ctrl->statuscount['revisi'] ?>)</td>
			<td width="12px"><div class="color06"></div></td><td width="70"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['validated'])?> (<?=$ctrl->statuscount['belumbayar'] ?>)</td>
		</tr>
		<tr>
			<td><div class="color08"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['paid'])?> (<?=$ctrl->statuscount['telahbayar'] ?>)</td>
			<td><div class="color09"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ready'])?> (<?=$ctrl->statuscount['siap'] ?>)</td>
			<td><div class="color10"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['delivered'])?> (<?=$ctrl->statuscount['delivered'] ?>)</td>
			<td><div class="color00"></div></td><td><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['cancelled'])?> (<?=$ctrl->statuscount['batal'] ?>)</td>
		</tr>
	</table>
	</center>
</div>
<br><br>
<div class="title">New Orders</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="right">Sisa Waktu</th>
	</tr>
	<?=$ctrl->neworders();?>
</table>

<br>
<div class="title">Orders to follow up</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="center">Status</th>
	</tr>
	<?=$ctrl->orderstofollowup();?>
</table>

<br>
<div class="title">Orders to deliver</div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="120" align="left">Order #</th>
		<th width="150" align="left">Date / Time</th>
		<th width="100" align="left">Member</th>
		<th width="100" align="right">Total</th>
		<th width="100" align="center">Status</th>
	</tr>
	<?=$ctrl->orderstodeliver();?>
</table>
<script language="javascript">
var oTime;
var RefreshEvery = 180; //in seconds
var ErrorRefreshEvery = 20*60; //in seconds
oTime = setTimeout(function(){requestupdatedata()},RefreshEvery * 1000);

function requestupdatedata() {  
	clearInterval(oTime);
	AJAXRequest(refreshdata,"process/bconlineorder_refresh.php");	
}

function refreshdata() {
	ajx = new AJAX(this);
	this.ajx = ajx;
	switch(ajx.status) {
		case "complete" :
			arr = ajx.respon.split('--datasplit--');
			bodyneworders.innerHTML = arr[0];
			bodyorderinprogress.innerHTML = arr[1];
			bodypendingorders.innerHTML = arr[2];
			oTime = setTimeout(function(){requestupdatedata()},RefreshEvery * 1000);
			break;
		case "error" :
			alert('Cannot refresh data, please check your internet connection');
			oTime = setTimeout(function(){requestupdatedata()},ErrorRefreshEvery * 1000);
			break;
		default :
			//waiting data
			break;
	}	
}
</script>

<?include "bcfooterleft.php";?>