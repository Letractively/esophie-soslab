		<div class="footer"> 
			<?if ($ctrl->filename() == 'mbrviewhistory.php') { ?>
				Online Orders 
			<? } else { ?>
				<a href="mbrviewhistory.php">Online Orders</a>
			<? } ?>
			&nbsp;&nbsp;|&nbsp;&nbsp; 
			<?if ($ctrl->filename() == 'mbrcekdata.php' || $ctrl->filename() == 'mbrpilihitem.php' || $ctrl->filename() == 'mbrordercheck.php' || $ctrl->filename() == 'mbrpaymentconfirm.php' ) { ?>
				Pesan Online 
			<? } else { 
				if ($ctrl->filename() == 'mbrviewhistory.php') 
				{
					if ( isset($ctrl->lastorderstatus) )
					{
						if ( $ctrl->lastorderstatus >= 1 && $ctrl->lastorderstatus <= 6 )
						{
			?>
							Pesan Online
			<?
						}
						else
						{
			?>
							<a href="mbrpilihitem.php">Pesan Online</a>
			<?
						}
					}
					else
					{
			?>
						Pesan Online
			<?
					}
				}
				else if ( isset($ctrl->statuscode) )
				{
					if ( $ctrl->statuscode == "1" )
					{
			?>
				Pesan Online
			<?		
					}
					else
					{
			?>
				<a href="mbrpilihitem.php">Pesan Online</a>
			<?		
					}
				}
				else
				{
			?>
				<a href="mbrpilihitem.php">Pesan Online</a>
			<?  }
			   }
			?>
			&nbsp;&nbsp;|&nbsp;&nbsp; 
			<?if ($ctrl->filename() == 'mbrdisclaimer.php') { ?>
				Terms and Conditions 
			<? } else { ?>
				<a href="mbrdisclaimer.php">Terms and Conditions</a>
			<? } ?>			
			</div>
			<div>Contact Customer Care: +62 01234567 | Sen-Jum 08:00-17:00</div>
		</div>
		</center>
	</form>
	</body>
</html>