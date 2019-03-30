<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Orb Index</title>
	<meta name="description" content="Questland Index"/>
	<link href="css/style.css" rel="stylesheet">
	<script src="sorttable.js"></script>
</head>
<body>
	<div class="wrapper">
<!--_____________________________________Header____________________________________ -->
	<header>
		<div class="header-contents">
			<div class="intro">Questland Index</div>
		</div>
	</header>
<!--_____________________________________Navigation____________________________________ -->
    <header>
		<nav class="website-nav">
			<ul>
				<li><a class="home-link" href="index.php">Home</a></li>
				<li><a href="character.php">Hero Manager</a></li>
				<li><a href="weaponindex.php">Weapon Index</a></li>
				<li><a href="gearindex.php">Gear Index</a></li>
			</ul>
		</nav> 
	</header>
<!--_____________________________________Page Content____________________________________ -->
<div class="center">
<?php
include 'dynamodb.php';

try {
	while (true){
		$result = $client->scan($params);
		echo '<table class="sortable">';
		echo "<tr>";
		echo '<th>Item Name</td>';
		echo '<th>Quality</td>';
		echo '<th>Slot</td>';
		echo '<th>Potential</td>';
		echo '<th>Health</td>';
		echo '<th>Attack</td>';
		echo '<th>Defense</td>';
		echo "</tr>";
		foreach ($result['Items'] as $value) {
			if($value['s']['S'] == 'rune'){
				echo "<tr>";
				echo '<td>',$value['n']['S'].'</td>';
				echo '<td>',$value['q']['S'].'</td>';
				echo '<td>',$value['s']['S'].'</td>';
				if (!empty($value['stats']['M']['hp']['L'])){
					echo '<td>',$value['stats']['M']['hp']['L'][1]['N'].'</td>';
					$HealthStat = $value['stats']['M']['hp']['L'][0]['N'];
					$AttackStat = "";
					$DefenseStat = "";
					
				}
				elseif (!empty($value['stats']['M']['dmg']['L'])){
					echo '<td>',$value['stats']['M']['dmg']['L'][1]['N'].'</td>';
					$AttackStat = $value['stats']['M']['dmg']['L'][0]['N'];
					$DefenseStat = "";
					$HealthStat = "";
				}
				elseif (!empty($value['stats']['M']['def']['L'])){
					echo '<td>',$value['stats']['M']['def']['L'][1]['N'].'</td>';
					$DefenseStat = $value['stats']['M']['def']['L'][0]['N'];
					$AttackStat = "";
					$HealthStat = "";
				}
				echo '<td>',$HealthStat.'</td>';
				echo '<td>',$AttackStat.'</td>';
				echo '<td>',$DefenseStat.'</td>';
				echo "</tr>";
			}
		}
		echo '</table>';
		if (isset($result['LastEvaluatedKey'])) {
			$params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
		} else {
			break;
		}
	}
} catch (DynamoDbException $e) {
    echo "Unable to scan:\n";
    echo $e->getMessage() . "\n";
}
?>
	</div>
</body>
</html>
