<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Orb Index"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700italic,400,300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="css/style-desktop.css" />
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
				<h1>Orb Index</h1>
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
			</ul>
		</nav> 
		</div>
</div>
<!--_____________________________________Page Content____________________________________ -->
<div id="page">
	<div class="hero" class="container">
<?php
include 'dynamodb.php';

try {
	while (true){
		$result = $client->scan($params);
		echo '<table class="sortable" style="width:500px">';
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
	</div>
</body>
</html>
