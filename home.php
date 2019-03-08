<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Character Manager</title>
	<meta name="description" content="Questland Hero Helper App"/>
	<link href="css/style.css" rel="stylesheet">
</head>
<script type="text/javascript">             
    function setImg() {
			var img = document.getElementById("imageToSwap");
					var index = document.getElementById("ComboBox");
			img.src = index.value;
    };
</script>
<body>
	<div class="wrapper">
<!--_____________________________________Header____________________________________ -->
		<header>
			<div class="header-contents">
				<div class="intro"><h1/>Questland Character Manager</div>
			</div>
		</header>
<!--_____________________________________Navigation____________________________________ -->
    <header>
		<nav class="website-nav">
			<ul>
				<li><a class="home-link" href="home.php">Home</a></li>
				<li><a href="character.php">Manage Hero</a></li>
				<li><a href="index.php">View Item Index</a></li>
			</ul>
		</nav> 
	</header>
<!--_____________________________________Page Content____________________________________ -->
		<div class="graphics">
			<div class="center">
				<p>What's New:</p>
			</div>
			<div class="descriptionText">
				<h1><p id="imgAlbum">Version: 0.0.1</p></h1>
				<h3><p id="imgTag">Created a base version of the questland character manager.</p></h3>
			</div>
		</div>


<!--_____________________________________Footer____________________________________ -->
	<footer>
		<p class="footer-contents">
			This helper is intended to help players maximize their builds and plan for transitioning to new armor sets.
			Gamesture is not affiliated with the creation and hosting of this website and is soley managed by a fan of the game.
		</p>
	</footer>
	</div>
</body>
</html>
