<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Home Page"/>
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
			<br>
			<h1>How To:</h1>
			<div class="descriptionText">
				<p>To use the Hero Inventory Manager, Guild Viewer, or the Gear Optimization Pages, you will need to retrieve your player token.
				This website does not retain any user data and your token is saved the the browser session.
				That means your token will remain active only for you, so long as your browser is opened.</p>
				<p>The Token can easily be retrieved by a packet sniffer or from your game logs.</p>
				<br>
				<h1>Do not give anyone your token that you don't trust with your Questland Data.</h1>
				<br>
			</div>
			<h3>iPhone Token Retrieval Instructions</h3>
			<div class="descriptionText">
				<p>The easiest way to retrive your token from an iPhone is to run a packet sniffer.
				<p>HTTPWatch is free and should work to display your token:
				<a href='https://itunes.apple.com/us/app/httpwatch-basic-http-sniffer/id658886056?mt=8'>HTTPWatch Basic - HTTP Sniffer</a>
				Look in any Questland API call for the value "token=" and copy the token to use for this site.</p><br>
				<br>
			</div class="descriptionText">
			<h3>Android Token Retrieval Instructions</h3>
			<p>Open "My Files" on your android and open your Internal Storage:
			<p>Android -> data -> com.gamesture.questland  -> files -> logs 
			<p>Inside the logs directory, you will see a file called "qllog_"
			<p>If you select and hold for 2 seconds, it will give you the option to Share. Otherwise, copy the log to another location, and open it with a log view app.
			<p>Once you have opened the file, Search for text "token=" and copy the token to use for this site.</p>

			<br><br>
				<h2>What's New:</h2>
			</div>
			<div class="descriptionText">
			<h1><p id="imgAlbum">Version: 0.2.3</p></h1>
				<p id="imgTag">Configured skills, talents, and elements data to be streamed instead of using a static file.</p>
			<h1><p id="imgAlbum">Version: 0.2.2</p></h1>
				<p id="imgTag">Fixed issue with Parts not getting associated with Elements.</p>
			<h1><p id="imgAlbum">Version: 0.2.0</p></h1>
				<p id="imgTag">Updated gear links for Swords and Spirit Update and fixed speed issues for hero page.</p>
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
