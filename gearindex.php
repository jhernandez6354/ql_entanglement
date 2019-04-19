<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Gear Index"/>
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
				<h1>Armor Index</h1>
			</div>
<!--_____________________________________Navigation____________________________________ -->
		<nav id="nav">
			<ul>
				<li class="active" ><a href="index.php">Home</a></li>
				<li><a href="hero.php">Hero</a></li>
				<li><a href="optimize.php">Optimize</a></li>
				<li><a href="guild.php">Guild</a></li>
				<li><a href="weaponindex.php">Weapons</a></li>
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
$setList = json_decode(file_get_contents("./wearable_sets"),true);
try {
	while (true){
		$result = $client->scan($params);
		echo '<table class="sortable" style="width:500px">';
		echo "<tr>";
		echo '<th>Item Name</td>';
		echo '<th>Element</td>';
		echo '<th>Quality</td>';
		echo '<th>Slot</td>';
		echo '<th>Potential</td>';
		echo '<th>Health</td>';
		echo '<th>HP Bonus</td>';
		echo '<th>Attack</td>';
		echo '<th>Dmg Bonus</td>';
		echo '<th>Defense</td>';
		echo '<th>Def Bonus</td>';
		echo '<th>Magic</td>';
		echo '<th>Magic Bonus</td>';
		echo '<th>Armor Link 1</td>';
		echo '<th>Link 1 Bonus Stat</td>';
		echo '<th>Link 1 Bonus %</td>';
		echo '<th>Armor Link 2</td>';
		echo '<th>Link 2 Bonus Stat</td>';
		echo '<th>Link 2 Bonus %</td>';
		echo '<th>Armor Link 3</td>';
		echo '<th>Link 3 Bonus Stat</td>';
		echo '<th>Link 3 Bonus %</td>';
		echo '<th>Orb Link 1</td>';
		echo '<th>Orb 1 Bonus Stat</td>';
		echo '<th>Orb 1 Bonus %</td>';
		echo '<th>Orb Link 2</td>';
		echo '<th>Orb 2 Bonus Stat</td>';
		echo '<th>Orb 2 Bonus #</td>';
		echo "</tr>";
		foreach ($result['Items'] as $value) {
			if (!empty($value['stats']['M']['def']['L'])){
				if ($value['s']['S'] != 'off_hand'){
					if($value['s']['S'] != 'main_hand'){
						if($value['s']['S'] != 'rune'){
							if ($value['q']['S'] != "common"){
								if ($value['q']['S'] != "uncommon"){
									foreach ($setList as $set){
										if ($value['set']["N"] == $set[0]) {
											$element = $set[1];
										}
									}
								}
							} 
							if (!isset($element)) {
								$element = "NA";
							}
							$potential = (int)$value['stats']['M']['hp']['L'][1]['N'] + (int)$value['stats']['M']['def']['L'][1]['N'] + (int)$value['stats']['M']['dmg']['L'][1]['N'] + (int)$value['stats']['M']['magic']['L'][1]['N'];
							echo "<tr>";
							echo '<td>',$value['n']['S'].'</td>';
							echo '<td>',$element.'</td>';
							echo '<td>',ucfirst($value['q']['S']).'</td>';
							echo '<td>',ucfirst($value['s']['S']).'</td>';
							echo '<td>',$potential.'</td>';
							echo '<td>',$value['stats']['M']['hp']['L'][0]['N'].'</td>';
							echo '<td>',$value['stats']['M']['hp']['L'][1]['N'].'</td>';
							echo '<td>',$value['stats']['M']['dmg']['L'][0]['N'].'</td>';
							echo '<td>',$value['stats']['M']['dmg']['L'][1]['N'].'</td>';
							echo '<td>',$value['stats']['M']['def']['L'][0]['N'].'</td>';
							echo '<td>',$value['stats']['M']['def']['L'][1]['N'].'</td>';
							echo '<td>',$value['stats']['M']['magic']['L'][0]['N'].'</td>';
							echo '<td>',$value['stats']['M']['magic']['L'][1]['N'].'</td>';
							if (!empty($value['ceff']['L'][0]['M'])){
								$alink1 = $value['ceff']['L'][0]['M']['n']['S'];
								$blink1 = $value['ceff']['L'][0]['M']['e']['S'];
								$clink1 = $value['ceff']['L'][0]['M']['v']['N'];
							} else {
								$alink1 = "";
								$blink1 = "";
								$clink1 = "";
							}
							if (!empty($value['ceff']['L'][1]['M'])){
								$alink2 = $value['ceff']['L'][1]['M']['n']['S'];
								$blink2 = $value['ceff']['L'][1]['M']['e']['S'];
								$clink2 = $value['ceff']['L'][1]['M']['v']['N'];
							} else {
								$alink2 = "";
								$blink2 = "";
								$clink2 = "";
							}
							if (!empty($value['ceff']['L'][2]['M'])){
								$alink3 = $value['ceff']['L'][2]['M']['n']['S'];
								$blink3 = $value['ceff']['L'][2]['M']['e']['S'];
								$clink3 = $value['ceff']['L'][2]['M']['v']['N'];
							} else {
								$alink3 = "";
								$blink3 = "";
								$clink3 = "";
							}
							if (!empty($value['ceff']['L'][3]['M'])){
								$alink4 = $value['ceff']['L'][3]['M']['n']['S'];
								$blink4 = $value['ceff']['L'][3]['M']['e']['S'];
								$clink4 = $value['ceff']['L'][3]['M']['v']['N'];
							} else {
								$alink4 = "";
								$blink4 = "";
								$clink4 = "";
							}
							if (!empty($value['ceff']['L'][4]['M'])){
								$alink5 = $value['ceff']['L'][4]['M']['n']['S'];
								$blink5 = $value['ceff']['L'][4]['M']['e']['S'];
								$clink5 = $value['ceff']['L'][4]['M']['v']['N'];
							} else {
								$alink5 = "";
								$blink5 = "";
								$clink5 = "";
							}
							echo '<td>',$alink1.'</td>';
							echo '<td>',$blink1.'</td>';
							echo '<td>',$clink1.'</td>';
							echo '<td>',$alink2.'</td>';
							echo '<td>',$blink2.'</td>';
							echo '<td>',$clink2.'</td>';
							echo '<td>',$alink3.'</td>';
							echo '<td>',$blink3.'</td>';
							echo '<td>',$clink3.'</td>';
							echo '<td>',$alink4.'</td>';
							echo '<td>',$blink4.'</td>';
							echo '<td>',$clink4.'</td>';
							echo '<td>',$alink5.'</td>';
							echo '<td>',$blink5.'</td>';
							echo '<td>',$clink5.'</td>';
							echo "</tr>";
						}
					}
				}
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
