<? if ($ctrl->login()) { ?>
<input type="hidden" id="menuselect" name="menuselect" value="<?=$ctrl->value('menuselect')?>">
<div class="boxcon" style="width:980px;margin-bottom:0px;margin-left:20px;">
	<div class="boxmenu"><a href="bconlineorder.php" class="menuselect">ONLINE ORDERS</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
	<div class="boxmenu"><a href="bcmyorder.php" class="menuselect">MY ORDERS</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
	<div class="boxmenu"><a href="bcreports.php" class="menuselect">REPORTS</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
	<div class="boxmenu"><a href="bclogout.php" class="menuselect">LOGOUT</a></div><div class="boxmenu" style="padding-right:10px;"><img src="images/menuright.jpg"/></div>
</div>
<? } ?>
