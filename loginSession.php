<?php
    session_start();
    if (isset($_POST['Submit'])) {
        $_SESSION['token'] = $_POST['token'];
        header("location:".$_SESSION['redirect']);
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Hero Manager"/>
	<link href="css/style.css" rel="stylesheet">
</head>

<div class="graphics">
   <form name="frmregister"action="<?= $_SERVER['PHP_SELF'] ?>" method="post" >
		<a id="adder">Enter Your Token:</a>
		<input type="text" id="token" name="token"><br>
		<input class="submit .transparentButton" type="submit" value="Submit" alt="Submit" title="Submit" name="Submit"/>
	</form>
</div>