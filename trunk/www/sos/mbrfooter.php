		<div class="footer"> 
			<?if ($ctrl->filename() == 'mbrviewhistory.php') { ?>
				Online Orders 
			<? } else { ?>
				<a href="mbrviewhistory.php">Online Orders</a>
			<? } ?>
			&nbsp;&nbsp;|&nbsp;&nbsp; 
			<?if ($ctrl->filename() == 'mbrcekdata.php' || $ctrl->filename() == 'mbrpilihitem.php') { ?>
				Pesan Online 
			<? } else { ?>
				<a href="mbrpilihitem.php">Pesan Online</a>
			<? } ?>
			&nbsp;&nbsp;|&nbsp;&nbsp; 
			<?if ($ctrl->filename() == 'mbrdisclaimer.php') { ?>
				Terms and Conditions 
			<? } else { ?>
				<a href="mbrdisclaimer.php">Terms and Conditions</a>
			<? } ?>			
			</div>
		</div>
		</center>
	</form>
	</body>
</html>