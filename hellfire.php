<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Hero Manager"/>
	<link href="css/style.css" rel="stylesheet">
</head>
<script src="jquery/jquery-3.3.1.min.js"></script>
<script src="jquery/aws-sdk-2.428.0.min.js"></script>
<script type="text/javascript" src="hellfire.js"></script>

<body>
	<div class="wrapper">
<!--_____________________________________Header____________________________________ -->
		<header>
			<div class="header-contents">
				<div class="logo">Character Manager</div>
			</div>
		</header>
<!--_____________________________________Navigation____________________________________ -->
    <header>
		<nav class="website-nav">
			<ul>
				<li><a class="home-link" href="index.php">Home</a></li>
                <li><a href="hero.php">Hero Manager</a></li>
				<li><a href="weaponindex.php">Weapon Index</a></li>
				<li><a href="gearindex.php">Gear Index</a></li>
				<li><a href="runeindex.php">Orb Index</a></li>
			</ul>
		</nav> 
	</header>
<!--_____________________________________Page Content____________________________________ -->
	<div class="graphics">
	<form id="form" name="getToken" method="post" action="">
			<a id="adder">Enter Your Token:</a>
			<input type="text" id="token" name="token"><br>
			<button id="btn_id" name="btn_id" value="button">Submit</button>
		</form>
		<div id="guild_hellfire"></div>
		<div id="guild_black"></div>
		<div id="guild_phoenix"></div>
		<div id="guild_forge"></div>
		<table id="table">
			<thead></thead>
		</table>
</div>
</body>
</html>
