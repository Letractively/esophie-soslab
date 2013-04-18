<?
	if (!isset($_COOKIE['abc']))
	{
		echo 'asdf';
		setcookie('abc','userid');
	}
	else
	{
		setcookie('abc','rubah');
	}
?>
<html>
	<body>
	<form id="abc" name="abc" method="post" action="tes.php">
		<?=$_COOKIE['abc']?>
		<input type="submit" value="OK">
	</form>
	</body>
</html>