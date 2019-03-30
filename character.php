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
<script type="text/javascript" src="hero.js"></script>

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
		<div id="player_name"></div>
		<div id="player_level"></div>
		<div id="equip_hero_gear">
			<div id="e_helm"></div>
			<div id="e_chest"></div>
			<div id="e_feet"></div>
			<div id="e_hand"></div>
			<div id="e_neck"></div>
			<div id="e_ring"></div>
			<div id="e_idol"></div>
			<div id="e_mh"></div>
			<div id="e_oh"></div>
		</div>
</div>
</body>
</html>
