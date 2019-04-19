<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Home Page"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700italic,400,300,700' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="jquery/skel.min.js"></script>
	<script src="jquery/skel-panels.min.js"></script>
	<script src="jquery/init.js"></script>
	<noscript>
		<link rel="stylesheet" href="src/skel-noscript.css" />
		<link rel="stylesheet" href="src/style.css" />
		<link rel="stylesheet" href="src/style-desktop.css" />
	</noscript>
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
				<h1>Home</h1>
			</div>
<!--_____________________________________Navigation____________________________________ -->
		<nav id="nav">
			<ul>
				<li class="active" ><a href="index.php">Home</a></li>
				<li><a href="hero.php">Hero</a></li>
				<li><a href="optimize.php">Optimize</a></li>
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
	<div class="hero" class="container">
			<br><br>
				<p>What's New:</p>
			</div>
			<br><br>
			<div class="descriptionText">
			<h1><p id="imgAlbum">Version: 0.1.1</p></h1>
				<p id="imgTag">Revised Website layout and added Mobile styling</p>
			<h1><p id="imgAlbum">Version: 0.1.0</p></h1>
				<p id="imgTag">Added the Guild Viewer and added additional information to the indexes.</p>
			<h1><p id="imgAlbum">Version: 0.0.1</p></h1>
				<p id="imgTag">Created a base version of the questland item and orb index.</p>
			</div>
		</div>
		</div>
		
<!--_____________________________________Footer____________________________________ -->
	<footer>
		<br><br>
		<p class="footer-contents">
			This helper is intended to help players maximize their builds and plan for transitioning to new armor sets.
			Gamesture is not affiliated with the creation and hosting of this website and is soley managed by a fan of the game.
		</p>
	</footer>
	</div>
</body>
</html>
