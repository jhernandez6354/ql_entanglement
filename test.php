<?php
if (!isset($_SESSION)) {
	session_start();
	$_SESSION['redirect'] = "optimize.php";
}
require_once('header.php')
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Hero Manager" />
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700italic,400,300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/style.css" />
</head>
<script src="jquery/jquery-3.3.1.min.js"></script>
<script src="jquery/aws-sdk-2.428.0.min.js"></script>
<script src="sorttable.js"></script>
<link rel="stylesheet" type="text/css" href="src/shadowbox.css">
<script type="test/javascript" src="src/shadowbox.js"></script>
<script type="test/javascript">
	Shadowbox.init({
    handleOversize: "drag",
    modal: true
});
</script>

<body>

	<!--_____________________________________Header____________________________________ -->
	<div id="header">
		<div class="container">
			<!--_____________________________________Logo____________________________________ -->
			<div id="logo">
				<h1>Character Manager</h1>
			</div>
			<!--_____________________________________Navigation____________________________________ -->
			<nav id="nav">
				<ul>
					<li class="active"><a href="index.php">Home</a></li>
					<li><a href="hero.php">Hero</a></li>
					<li><a href="guild.php">Guild</a></li>
					<li><a href="weaponindex.php">Weapons</a></li>
					<li><a href="gearindex.php">Armor</a></li>
					<li><a href="runeindex.php">Orbs</a></li>
				</ul>
			</nav>
		</div>
	</div>

	<!--_____________________________________Page Content____________________________________ -->
	<div id="page">
		<div class="opti_grid">
			<?php
				$testarray = array();
				$testarray['t']['def'] = 1;
				$testarray['t']['hp'] = 2;
				$testarray['w']['def'] = 3;
				
				echo '<pre>'; print_r($testarray); echo '</pre>';
			?>
			<div class="mark_placeholder"></div>
			<div class="mark_placeholder_txt"></div>


		</div>
		<div class="hero" class="container">
			<div class="descriptionText">
				<br><br><br><br>
				<h2>Work In Progress</h2>
				<p>The information displayed above is still in testing and incomplete.</p>
			</div>
		</div>
	</div>
</body>

</html>