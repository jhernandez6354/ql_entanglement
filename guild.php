<?php 
if(!isset($_SESSION)) 
{ 
	session_start();
	$_SESSION['redirect'] = "guild.php";
}
require_once('header.php')
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Hero Manager"/>
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
				<h1>Character Manager</h1>
			</div>
<!--_____________________________________Navigation____________________________________ -->
		<nav id="nav">
			<ul>
				<li class="active" ><a href="index.php">Home</a></li>
                <li><a href="hero.php">Hero Manager</a></li>
				<li><a href="weaponindex.php">Weapon Index</a></li>
				<li><a href="gearindex.php">Gear Index</a></li>
				<li><a href="runeindex.php">Orb Index</a></li>
			</ul>
		</nav> 
	</div>
</div>
<!--_____________________________________Page Content____________________________________ -->
<div id="page">
	<div class="hero" class="container">
		
		<?php
			$token = $_SESSION['token'];
			include 'dynamodb.php';
			$ch=curl_init();
			$curl_headers=array(
				"token: $token",
			);
			$proxy='https://cors-anywhere.herokuapp.com/';
			curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/rankings/getguildranking/?type=battle_event_guild');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$guild_id = json_decode(curl_exec($ch), true);
			curl_close($ch);
			$guild = $guild_id["data"]["hero2"]["guild_id"]; //Target a specific value for this to work.
			foreach($guild_id["data"]["guilds_list"] as $guilds){
				if ($guilds["id"] == $guild){
					$guild_name = $guilds['name'];
					echo "<br>";
					echo $guild_name;
					echo "<br>";
					break;
				}
			}
			foreach($guild_id["data"]["guild_rankings"]["battle_event_guild"]["ladder2"][0] as $guildrank){
				if ($guilds["id"] == $guildrank[1]){
					$guild_rank = "Gold";
					break;
				}
			}
			if (!$guild_rank){
				foreach($guild_id["data"]["guild_rankings"]["battle_event_guild"]["ladder2"][1] as $guildrank){
					if ($guilds["id"] == $guildrank[1]){
						$guild_rank = "Silver";
						break;
					}
				}
			}
			if (!$guild_rank){
				foreach($guild_id["data"]["guild_rankings"]["battle_event_guild"]["ladder2"][2] as $guildrank){
					if ($guilds["id"] == $guildrank[1]){
						$guild_rank = "Silver";
						break;
					}
				}
			}
			echo "Battle Event Rank: ".$guild_rank;
			echo "<br>";
			echo "Battle Event Position: ".$guild_id["data"]["guild_rankings"]["battle_event_guild"]["my_guild"][0];
			echo "<br>";
			echo "<br>";
			echo '<table class="sortable">';
			echo "<tr>";
			echo '<th>Hero Name</td>';
			echo '<th>Level</td>';
			echo '<th>Power</td>';
			echo '<th>Guild Rank</td>';
			echo '<th>Guild BE Place</td>';
			echo '<th>Battle Event Score</td>';
			echo "</tr>";
			foreach($guild_id["data"]["heroes_avatars"] as $key => $heros){
				$hero_name = $heros["h"][0];
				$hero_level = $heros["h"][1];
				$hero_power = number_format($heros["h"][3]);
				$hero_rank = $heros["g"][1];
				foreach($guild_id["data"]["guild_rankings"]["battle_event_guild"]["ladder_members"] as $player){
					if($player[1] == $key){
						$hero_score = number_format($player[2]);
						$hero_place = $player[0];
					}
				}
				echo "<tr>";
				echo '<td>',$hero_name.'</td>';
				echo '<td>',$hero_level.'</td>';
				echo '<td>',$hero_power.'</td>';
				echo '<td>',$hero_rank.'</td>';
				echo '<td>',$hero_place.'</td>';
				echo '<td>',$hero_score.'</td>';
				echo "</tr>";
			}
			echo '</table>';
		?>
		</div>
</div>
</body>
</html>
