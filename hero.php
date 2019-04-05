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
	<link href="css/style.css" rel="stylesheet">
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
                <li><a href="guild.php">Guild View</a></li>
				<li><a href="weaponindex.php">Weapon Index</a></li>
				<li><a href="gearindex.php">Gear Index</a></li>
				<li><a href="runeindex.php">Orb Index</a></li>
			</ul>
		</nav> 
	</header>
<!--_____________________________________Page Content____________________________________ -->
	<div class="graphics">
		
		<?php
			error_reporting(0);
			$token = $_SESSION['token'];
			include 'dynamodb.php';
			$ch=curl_init();
			$curl_headers=array(
				"token: $token",
			);
			
			//await fetch(cors_api_url + `http://149.56.27.225/user/getprofile/` + '/?hero_id=' + playerid ,initObject)

			$proxy='https://cors-anywhere.herokuapp.com/';
			curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/client/init/');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$get_id = json_decode(curl_exec($ch), true);
			curl_close($ch);
			$player_id = $get_id['data']['hero']['id'];
			echo "<br>";
			echo "Name: ".$get_id['data']['hero']['name'];
			echo "<br>";
			echo "Level: ".$get_id['data']['hero']['level'];
			echo "<br>";
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://149.56.27.225/user/getprofile/?hero_id='.$player_id);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$player_data = json_decode(curl_exec($ch), true);
			curl_close($ch);
			echo "Global Rank: ".$player_data['data']["profile_heropower_rank"]["rank"];
			echo "<br>";
			echo "<br>";
			echo '<table class="sortable">';
			echo "<tr>";
			echo '<th>Item</td>';
			echo '<th>Slot</td>';
			echo '<th>Location</td>';
			echo '<th>Quality</td>';
			echo '<th>Upgrade</td>';
			echo '<th>Boost</td>';
			echo "<tr>";
			foreach($player_data['data']['profile_items_list'] as $items){
				$value = $items['a'][1];
				$dbquery = [
					'TableName' => 'ql_dynamo',
					'KeyConditionExpression' => '#tag = :t',
					'ExpressionAttributeNames'=> [ '#tag' => 't' ],
					'ExpressionAttributeValues'=> array( ':t'  => array('N' => "$value"))
				];
				$value = $client->query($dbquery);
				if ($items["wear"]){ //Item is gear
					if($items["wear"][0] == 1){
						$indexed = "Equip";
						$name = $value['Items'][0]['n']["S"];
						$slot = $value['Items'][0]['s']["S"];
						$quality = $value['Items'][0]['q']["S"];
						$upgrade = $items["wear"][1];
						$boost = $items["wear"][2];
					} 
					elseif ($items["wear"][0] == 2) {
						$indexed = "Collection 1";
						$name = $value['Items'][0]['n']["S"];
						$slot = $value['Items'][0]['s']["S"];
						$quality = $value['Items'][0]['q']["S"];
						$upgrade = $items["wear"][1];
						$boost = $items["wear"][2];
					}
					elseif ($items["wear"][0] == 3) {
						$indexed = "Collection 2";
						$name = $value['Items'][0]['n']["S"];
						$slot = $value['Items'][0]['s']["S"];
						$quality = $value['Items'][0]['q']["S"];
						$upgrade = $items["wear"][1];
						$boost = $items["wear"][2];
					} else {
						$indexed = "Inventory";
						$name = $value['Items'][0]['n']["S"];
						$slot = $value['Items'][0]['s']["S"];
						$quality = $value['Items'][0]['q']["S"];
						$upgrade = $items["wear"][1];
						$boost = $items["wear"][2];
					}
				} elseif ($items["orb"]){ //Item is an orb
					$indexed = "Orb";
					$name = $value['Items'][0]['n']["S"];
					$slot = $value['Items'][0]['s']["S"];
					$quality = $value['Items'][0]['q']["S"];
					$upgrade = $items["orb"][1];
					$boost = $items["orb"][2];
				} else {
					$indexed = "Special";
					$name = $value['Items'][0]['n']["S"];
					$slot = $value['Items'][0]['s']["S"];
					$quality = $value['Items'][0]['q']["S"];
					$upgrade = "NA";
					$boost = "NA";
				}
				echo "<tr>";
				echo '<td>',$name.'</td>';
				echo '<td>',$slot.'</td>';
				echo '<td>',$indexed.'</td>';
				echo '<td>',$quality.'</td>';
				echo '<td>',$upgrade.'</td>';
				echo '<td>',$boost.'</td>';
				echo "</tr>";
			}
			echo '</table>';
		?>
</div>
</body>
</html>
