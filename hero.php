<?php 
if(!isset($_SESSION)) 
{ 
	session_start();
	$_SESSION['redirect'] = "hero.php";
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
		<?php
			include 'dynamodb.php';
			$token = $_SESSION['token'];
			$ch=curl_init();
			$curl_headers=array(
				"token: $token",
			);
			$proxy='https://cors-anywhere.herokuapp.com/';
			curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/client/init/');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$get_hero = json_decode(curl_exec($ch), true);
			curl_close($ch);
			$player_id = $get_hero['data']['hero']['id'];
			$SESSION['player_id'] = $player_id;
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/user/getprofile/?hero_id='.$player_id);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$player_data = json_decode(curl_exec($ch), true);
			curl_close($ch);
			echo "Name: ".$get_hero['data']['hero']['name'];
			echo "<br>";
			echo "Level: ".$get_hero['data']['hero']['level'];
			echo "<br>";
			echo "Power: ".number_format($player_data['data']['profile']['attributes']['heropower']);
			echo "<br>";
			echo "Global Rank: ".$player_data['data']["profile_heropower_rank"]["rank"];
			echo "<br>";
			echo "<br>";
		?>
	</div>
	<div class="graphics">
		<?php
			if(isset($_SESSION['token'])){
				error_reporting(0);
				$token = $_SESSION['token'];
				include 's3.php';
				include 'dynamodb.php';
				$setList = json_decode(file_get_contents("./wearable_sets"),true);
				$weaponPassive = json_decode(file_get_contents("./static_passive_skills"),true);
				$s3Items = array();
				$bucket = 'elasticbeanstalk-us-east-1-331694059185';
				$s3objects = $s3Client->getIterator('ListObjects', array(
					'Bucket' => $bucket,
					'Prefix' => 'resources/storage/'
				));
				foreach ($s3objects as $object) {
					$s3Items[] = $object;
				}
				$ch=curl_init();
				$curl_headers=array(
					"token: $token",
				);
				
				$player_id =$SESSION['player_id'];
				curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/user/getprofile/?hero_id='.$player_id);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$player_data = json_decode(curl_exec($ch), true);
				curl_close($ch);
				echo '<table class="sortable" style="width:500px">';
				echo "<tr>";
				echo '<th>Item</td>';
				echo '<th>Element</td>';
				echo '<th>Slot</td>';
				echo '<th>Location</td>';
				echo '<th>Quality</td>';
				echo '<th>Upgrade</td>';
				echo '<th>Boost</td>';
				echo '<th>Parts</td>';
				echo "<tr>";
				foreach($get_hero['data']['items_list'] as $items){
					$value = $items['a'][1];
					$dbquery = [
						'TableName' => 'ql_dynamo',
						'KeyConditionExpression' => '#tag = :t',
						'ExpressionAttributeNames'=> [ '#tag' => 't' ],
						'ExpressionAttributeValues'=> array( ':t'  => array('N' => "$value"))
					];
					$value = $client->query($dbquery);
					if (isset($items["wear"])){ //Item is gear
						if($items["wear"][0] == 1){
							$indexed = "Equip";
							$image = $value['Items'][0]['i_sd']["N"];
							$name = $value['Items'][0]['n']["S"];
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($value['Items'][0]['set']["N"] == $set[0]) {
											$element = $set[1];
										}
									}
								}
							} 
							if (!isset($element)) {
								$element = "NA";
							}
							$upgrade = $items["wear"][1];
							$boost = $items["wear"][2];
							$parts = 1;
							#links
							if (isset($value['Items'][0]['ceff']["L"][0]["M"])){
								$link1 = $items["wear"][3][0];
								$link2 = $items["wear"][3][1];
								$link3 = $items["wear"][3][2];
								$link4 = $items["wear"][3][3];
								$link5 = $items["wear"][3][4];
								$db_link1_name =  $value['Items'][0]['ceff']["L"][0]["M"]["n"]["S"];
								$db_link1_enhance =  ucfirst($value['Items'][0]['ceff']["L"][0]["M"]["e"]["S"]);
								$db_link1_bonus =  $value['Items'][0]['ceff']["L"][0]["M"]["v"]["N"];
								$db_link2_name =  $value['Items'][0]['ceff']["L"][1]["M"]["n"]["S"];
								$db_link2_enhance =  ucfirst($value['Items'][0]['ceff']["L"][1]["M"]["e"]["S"]);
								$db_link2_bonus =  $value['Items'][0]['ceff']["L"][1]["M"]["v"]["N"];
								$db_link3_name =  $value['Items'][0]['ceff']["L"][2]["M"]["n"]["S"];
								$db_link3_enhance =  ucfirst($value['Items'][0]['ceff']["L"][2]["M"]["e"]["S"]);
								$db_link3_bonus =  $value['Items'][0]['ceff']["L"][2]["M"]["v"]["N"];
								$db_link4_name =  $value['Items'][0]['ceff']["L"][3]["M"]["n"]["S"];
								$db_link4_enhance = ucfirst( $value['Items'][0]['ceff']["L"][3]["M"]["e"]["S"]);
								$db_link4_bonus =  $value['Items'][0]['ceff']["L"][3]["M"]["v"]["N"];
								$db_link5_name =  $value['Items'][0]['ceff']["L"][4]["M"]["n"]["S"];
								$db_link5_enhance =  ucfirst($value['Items'][0]['ceff']["L"][4]["M"]["e"]["S"]);
								$db_link5_bonus =  $value['Items'][0]['ceff']["L"][4]["M"]["v"]["N"];
							}
							if (isset($value['Items'][0]['pskls']["L"])){
								$db_passive1_effect = $value['Items'][0]['pskls']["L"][0]["N"];
								$db_passive2_effect = $value['Items'][0]['pskls']["L"][1]["N"];
							}
							foreach ($s3Items as $object) {
								if (strpos($object['Key'], $image) !== false) {
									$key = $object['Key'];
									break;
								}
							}
							echo "<div class=\"$slot"."_txt\">$name</div>";
							echo "<div class=\"$slot\">";
								echo "<div class=\"equiphover\"><img id=\"$slot"."_img\" src=\"https://s3.amazonaws.com/$bucket/$key\" width=\"50\" height=\"50\"></img>";
								
								if (isset($value['Items'][0]['ceff']["L"][0]["M"])){
									echo "<span class=\"tooltip\">
										<div class=\"link1\">$db_link1_name: $db_link1_enhance $db_link1_bonus%</div>
										<div class=\"link2\">$db_link2_name: $db_link2_enhance $db_link2_bonus%</div>
										<div class=\"link3\">$db_link3_name: $db_link3_enhance $db_link3_bonus%</div>
										<div class=\"link4\">$db_link4_name: $db_link4_enhance $db_link4_bonus%</div>
										<div class=\"link5\">$db_link5_name: $db_link5_enhance $db_link5_bonus%</div>
									</span>"
									;
								}
								if (isset($db_passive1_effect)){
									if ($db_passive1_effect != "NA"){
										foreach ($weaponPassive as $key => $passive){
											if ($db_passive1_effect == $key) {
												$db_passive1_effect = $passive["d"];
											}
										};
									}
									if ($db_passive1_effect != "NA"){
										foreach ($weaponPassive as $key => $passive){
											if ($db_passive2_effect == $key) {
												$db_passive2_effect = $passive["d"];
											}
										};
									}
									echo "<span class=\"tooltip\">
										<div class=\"passive1\">Main: $db_passive1_effect</div>
										<div class=\"passive1\">Secondary: $db_passive2_effect</div>
									</span>";
									echo "</div>";
								}
							echo "</div>";
						} 
						elseif ($items["wear"][0] == 2) {
							$indexed = "Collection 1";
							$name = $value['Items'][0]['n']["S"];
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($value['Items'][0]['set']["N"] == $set[0]) {
											$element = $set[1];
										}
									}
								}
							} 
							if (!isset($element)) {
								$element = "NA";
							}
							$upgrade = $items["wear"][1];
							$boost = $items["wear"][2];
							$parts = 1;
						}
						elseif ($items["wear"][0] == 3) {
							$indexed = "Collection 2";
							$name = $value['Items'][0]['n']["S"];
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($value['Items'][0]['set']["N"] == $set[0]) {
											$element = $set[1];
										}
									}
								}
							} 
							if (!isset($element)) {
								$element = "NA";
							}
							$upgrade = $items["wear"][1];
							$boost = $items["wear"][2];
							$parts = 1;
						} else {
							$indexed = "Inventory";
							$name = $value['Items'][0]['n']["S"];
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($value['Items'][0]['set']["N"] == $set[0]) {
											$element = $set[1];
										}
									}
								}
							} 
							if (!isset($element)) {
								$element = "NA";
							}
							$upgrade = $items["wear"][1];
							$boost = $items["wear"][2];
							$parts = 1;
						}
					} elseif (isset($items["orb"])){ //Item is an orb
						if ($items["orb"][0] != null) {
							$indexed = "Orb";
							$name = $value['Items'][0]['n']["S"];
							$element = "NA";
							$slot = ucfirst($items["orb"][0]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							$upgrade = $items["orb"][1];
							$boost = $items["orb"][2];
							$parts = 1;
						}else {
							$indexed = "Orb";
							$name = $value['Items'][0]['n']["S"];
							$element = "NA";
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							$upgrade = $items["orb"][1];
							$boost = $items["orb"][2];
							$parts = 1;
						}
					} else {
						if ($value['Items'][0]['s']["S"] == "exactshard") {
							$indexed = "Parts";
							$name = $value['Items'][0]['n']["S"];
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							$upgrade = 0;
							$boost = 0;
							$parts = number_format($items['a'][5]);
							foreach ($setList as $set){
								if ($value['Items'][0]['set']["N"] == $set[0]) {
									$element = $set[1];
								}
							}
							if (!isset($element)) {
								$element = "NA";
							}
						} else {
							$indexed = "Special";
							$name = $value['Items'][0]['n']["S"];
							$element = "NA";
							$slot = ucfirst($value['Items'][0]['s']["S"]);
							$quality = ucfirst($value['Items'][0]['q']["S"]);
							$upgrade = 0;
							$boost = 0;
							$parts = number_format($items['a'][5]);
							
						}
						
					}
					echo "<tr>";
					echo '<td>',$name.'</td>';
					echo '<td>',$element.'</td>';
					echo '<td>',$slot.'</td>';
					echo '<td>',$indexed.'</td>';
					echo '<td>',$quality.'</td>';
					echo '<td>',$upgrade.'</td>';
					echo '<td>',$boost.'</td>';
					echo '<td>',$parts.'</td>';
					echo "</tr>";
				}
				echo '</table>';
			}
		?>
		<div class="mark_placeholder"></div>
		<div class="mark_placeholder_txt"></div>
	</div>
</div>
</body>
</html>
