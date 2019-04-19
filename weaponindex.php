<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Weapon Index"/>
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
				<h1>Weapon Index</h1>
			</div>
<!--_____________________________________Navigation____________________________________ -->
		<nav id="nav">
			<ul>
				<li class="active" ><a href="index.php">Home</a></li>
				<li><a href="hero.php">Hero</a></li>
                <li><a href="guild.php">Guild</a></li>
				<li><a href="optimize.php">Optimize</a></li>
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
#error_reporting(0);
include 'dynamodb.php';
$setList = json_decode(file_get_contents("./wearable_sets"),true);
$weaponPassive = json_decode(file_get_contents("./static_passive_skills"),true);
try {
	while (true){
		$result = $client->scan($params);
		echo '<table class="sortable" style="width:1300px">';
		echo "<tr>";
		echo '<th>Item Name</td>';
		echo '<th>Element</td>';
		echo '<th>Quality</td>';
		echo '<th>Slot</td>';
		echo '<th>Potential</td>';
		echo '<th>Health</td>';
		echo '<th>Attack</td>';
		echo '<th>Defense</td>';
		echo '<th>Magic</td>';
		echo '<th>Primary Skill</td>';
		echo '<th>Passive Skill</td>';
		echo "</tr>";
		foreach ($result['Items'] as $value) {
			if ($value['s']['S'] == 'off_hand' || $value['s']['S'] == 'main_hand'){
				if (isset($value['pskls']['L'])){
					$potential = (int)$value['stats']['M']['hp']['L'][1]['N'] + (int)$value['stats']['M']['def']['L'][1]['N'] + (int)$value['stats']['M']['dmg']['L'][1]['N'] + (int)$value['stats']['M']['magic']['L'][1]['N'];
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
					if (isset($value['pskls']["L"])){
						if (isSet($value['pskls']["L"][0]["N"])){
							$db_passive1_effect = $value['pskls']["L"][0]["N"];
						}else {
							$db_passive1_effect = "NA";
						}
						if (isSet($value['pskls']["L"][1]["N"])){
							$db_passive2_effect = $value['pskls']["L"][1]["N"];
						}else {
							$db_passive2_effect = "NA";
						}
						
					}
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
					if (strpos($db_passive1_effect, 'adds <color') !== true){
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b">',' <font color=#5dd8f6>4 blue</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w">',' <font color=#f9e9f1>4 white</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r">',' <font color=#ff5400>4 red</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b">',' <font color=#5dd8f6>2 blue</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w">',' <font color=#f9e9f1>2 white</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r">',' <font color=#ff5400>2 red</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="b">',' <font color=#5dd8f6>1 blue</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="w">',' <font color=#f9e9f1>1 white</font>',$db_passive1_effect);
						$db_passive1_effect= str_replace('<sprite="skill_atlas" name="r">',' <font color=#ff5400>1 red</font>',$db_passive1_effect);
					}
					$db_passive1_effect= str_replace('<color=',' <font color=',$db_passive1_effect);
					$db_passive1_effect= str_replace('#ATTR_OTHER#','',$db_passive1_effect);
					$db_passive1_effect= str_replace('<sprite="skill_atlas"','</font><sprite="skill_atlas"',$db_passive1_effect);
					$db_passive1_effect= str_replace('#SHIELD#','5%',$db_passive1_effect);
					if (strpos($db_passive2_effect, 'adds <color') !== true){
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b">',' <font color=#5dd8f6>4 blue</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w">',' <font color=#f9e9f1>4 white</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r">',' <font color=#ff5400>4 red</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="b"><sprite="skill_atlas" name="b">',' <font color=#5dd8f6>2 blue</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="w"><sprite="skill_atlas" name="w">',' <font color=#f9e9f1>2 white</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="r"><sprite="skill_atlas" name="r">',' <font color=#ff5400>2 red</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="b">',' <font color=#5dd8f6>1 blue</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="w">',' <font color=#f9e9f1>1 white</font>',$db_passive2_effect);
						$db_passive2_effect= str_replace('<sprite="skill_atlas" name="r">',' <font color=#ff5400>1 red</font>',$db_passive2_effect);
					}
					$db_passive2_effect= str_replace('<color=',' <font color=',$db_passive2_effect);
					$db_passive2_effect= str_replace('#ATTR_OTHER#','',$db_passive2_effect);
					$db_passive2_effect= str_replace('<sprite="skill_atlas"','</font><sprite="skill_atlas"',$db_passive2_effect);
					$db_passive2_effect= str_replace('#SHIELD#','5%',$db_passive2_effect);
					echo "<tr>";
					echo '<td>',$value['n']['S'].'</td>';
					echo '<td>',$element.'</td>';
					echo '<td>',ucfirst($value['q']['S']).'</td>';
					echo '<td>',ucfirst($value['s']['S']).'</td>';
					echo '<td>',$potential.'</td>';
					echo '<td>',$value['stats']['M']['hp']['L'][0]['N'].'</td>';
					echo '<td>',$value['stats']['M']['dmg']['L'][0]['N'].'</td>';
					echo '<td>',$value['stats']['M']['def']['L'][0]['N'].'</td>';
					echo '<td>',$value['stats']['M']['magic']['L'][0]['N'].'</td>';
					echo '<td>',$db_passive1_effect.'</td>';
					echo '<td>',$db_passive2_effect.'</td>';
					echo "</tr>";
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
