<?include "bcheaderleft.php";?>

<div class="title">NEW ORDERS<a id="neworder">&nbsp;</a></div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="100" align="left">Order #</th>
		<th width="110" align="left">Date / Time</th>
		<th width="230" align="left">Member</th>
		<th width="80" align="right">Total</th>
		<th width="120" align="right">Sisa Waktu</th>
	</tr>
	<tbody id="bodyneworders"></tbody>
</table>

<br>
<div class="title">ORDERS IN PROGRESS<a id="followup">&nbsp;</a></div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="100" align="left">Order #</th>
		<th width="110" align="left">Date / Time</th>
		<th width="230" align="left">Member</th>
		<th width="80" align="right">Total</th>
		<th width="120" align="center">Status</th>
	</tr>
	<tbody id="bodyorderinprogress"></tbody>
</table>

<br>
<div class="title">ORDERS TO DELIVER<a id="deliverorder">&nbsp;</a></div>
<table class="dataview">
	<tr>
		<th width="20">&nbsp;</th>
		<th width="100" align="left">Order #</th>
		<th width="110" align="left">Date / Time</th>
		<th width="230" align="left">Member</th>
		<th width="80" align="right">Total</th>
		<th width="120" align="center">Status</th>
	</tr>
	<tbody id="bodypendingorders"></tbody>
</table>

<br><br>
<div class="boxcon5" style="width:730px">
	<center>
	<table width="100%">
		<tr>
			<td width="12px"><div class="color01"></div></td><td width="70"><a href="#neworder" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ordered'])?> (<?=$ctrl->statuscount['orderbaru'] ?>)</a></td>
			<td width="12px"><div class="color02"></div></td><td width="70"><a href="#followup" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['bypassed'])?> (<?=$ctrl->statuscount['dalamproses'] ?>)</a></td>
			<td width="12px"><div class="color05"></div></td><td width="50"><a href="#followup" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['edited'])?> (<?=$ctrl->statuscount['revisi'] ?>)</a></td>
			<td width="12px"><div class="color06"></div></td><td width="70"><a href="#followup" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['validated'])?> (<?=$ctrl->statuscount['belumbayar'] ?>)</a></td>
		</tr>
		<tr>
			<td><div class="color07"></div></td><td><a href="#followup" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['confirmed'])?> (<?=$ctrl->statuscount['confirmed'] ?>)</a></td>
                        <td><div class="color08"></div></td><td><a href="#deliverorder" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['paid'])?> (<?=$ctrl->statuscount['telahbayar'] ?>)</a></td>
			<td><div class="color09"></div></td><td><a href="#deliverorder" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['ready'])?> (<?=$ctrl->statuscount['siap'] ?>)</a></td>
			<td><div class="color00"></div></td><td><a href="#followup" class="normal"><?=$ctrl->colorstatuslabel($ctrl->sysparam['salesstatus']['cancelled'])?> (<?=$ctrl->statuscount['batal'] ?>)</a></td>
		</tr>
	</table>
	</center>
</div>

<script language="javascript">
var oTime;
var RefreshEvery = 60; //in seconds 
var ErrorRefreshEvery = 20*60; //in seconds
oTime = setTimeout(function(){requestupdatedata()},RefreshEvery * 1000);


window.onload = function()
                {
                   requestupdatedata();
                };
		
function requestupdatedata() {  
	clearInterval(oTime);
	AJAXRequest(refreshdata,"process/bconlineorder_refresh.php");	
}

function refreshdata() {
	ajx = new AJAX(this);
	this.ajx = ajx;
	switch(ajx.status) {
		case "complete" :
			var bodyneworders=document.getElementById("bodyneworders");
			var bodyorderinprogress=document.getElementById("bodyorderinprogress");
			var bodypendingorders=document.getElementById("bodypendingorders");

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