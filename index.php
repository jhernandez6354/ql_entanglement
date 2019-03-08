<?php
require_once('header.php');
$name = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>MyTosis - My Images</title>
	<meta name="description" content="Questland "/>
	<link href="css/style.css" rel="stylesheet">
</head>
<body>
	<div class="wrapper">
<!--_____________________________________Header____________________________________ -->
		<header>
			<div class="header-contents">
				<div class="intro"><h1/>Questland Index</div>
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
		<h1>Welcome <?php 
			$login_session=$_SESSION['username'];
			echo $login_session;?>
		</h1>
	</header>
<!--_____________________________________Page Content____________________________________ -->
		<div class="graphics">
			<div>
				<?php
					include('config.php');
					$items = $pdo->query("SELECT * FROM item_Armor");
					while( $row = $images->fetch(PDO::FETCH_ASSOC) ) { ?>
					<?php $param = $row['name']; ?>
					<?php $imgNm = $row['type']; ?>
					<?php $imgDesc = $row['desc']; ?>
				<div class="descriptionText">
					<img id="imageToSwap" class="fit" width="10%" height="10%" src="<?php echo $param; ?>" rel=”shadowbox”/>
					<h1><p id="name"><?php echo $imgNm; ?></p></h1>
					<h3><p id="type"><?php echo $imgDesc; ?></p></h3>

				</div>
				<?php } ?>
		</div>
	</div>
</body>
</html>
