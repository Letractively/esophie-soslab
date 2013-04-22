<? if ($ctrl->login()) { ?>
<input type="hidden" id="menuselect" name="menuselect" value="<?=$ctrl->value('menuselect')?>">
<div class="boxcon" style="width:980px;margin-bottom:0px;">
	<div class="boxleft" style="width:530px;">
		<div class="boxmenu"><a href="bconlineorder.php" class="menuselect">ONLINE ORDERS</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
		<div class="boxmenu"><a href="bcreport02.php" class="menuselect">ORDER HISTORY</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
		<div class="boxmenu"><a href="bcreport01.php" class="menuselect">STOCK ON HOLD</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
		<div class="boxmenu"><a href="bcreport03.php" class="menuselect">CREDIT BC REKAP</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>		
	</div>
	<div class="boxright" style="width:430px;">
		<div class="boxmenu2"><a href="bclogout.php" class="menuselect">LOGOUT</a></div>		
		<div class="boxmenu2" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>		
		<div class="boxmenu2">BC #<?=$ctrl->userid()?></div>
	</div>
</div>
<? } ?>
