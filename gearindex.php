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
$set_itemList[] =array();
$item_list[] = array();
$setList = json_decode(file_get_contents("./wearable_sets"),true);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android');
$current_update = json_decode(curl_exec($ch));
$set_itemlist = $current_update->data->static_data->crc_details->item_templates;
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$set_itemlist/item_templates/");
$item_list = json_decode(curl_exec($ch));
curl_close($ch);

try {
	while (true){
		$result = $client->scan($params);
		$array_Result[] = $result['Items'];
		echo '<table class="sortable" style="width:500px">';
		echo "<tr>";
		echo '<th>Item Name</td>';
		echo '<th>Element</td>';
		echo '<th>Quality</td>';
		echo '<th>Slot</td>';
		echo '<th>Potential</td>';
		echo '<th>Health</td>';
		echo '<th>HP Boost</td>';
		echo '<th>Attack</td>';
		echo '<th>Dmg Boost</td>';
		echo '<th>Defense</td>';
		echo '<th>Def Boost</td>';
		echo '<th>Magic</td>';
		echo '<th>Magic Boost</td>';
		echo '<th>Armor Bonus Stat</td>';
		echo '<th>Armor Bonus %</td>';
		echo '<th>Armor Link 1</td>';
		echo '<th>Armor Link 2</td>';
		echo '<th>Armor Link 3</td>';
		echo '<th>Orb Bonus Stat</td>';
		echo '<th>Orb Bonus %</td>';
		echo '<th>Orb Link 1</td>';
		echo '<th>Orb Link 2</td>';
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
							if (!empty($value['links']['L'][0]['M'])){
								$aenhance = $value['links']['L'][0]['M']['e']['S'];
								$aenhance_per = $value['links']['L'][0]['M']['p']['N'];
								if (isset($value['links']['L'][0]['M']['i']['L'][0]['L'])){
									$alink1_tag = $value['links']['L'][0]['M']['i']['L'][0]['L'][0]['N'];
								}
								if (isset($value['links']['L'][0]['M']['i']['L'][1]['L'])){
									$alink2_tag = $value['links']['L'][0]['M']['i']['L'][1]['L'][0]['N'];
								}
								if (isset($value['links']['L'][0]['M']['i']['L'][2]['L'])){
									$alink3_tag = $value['links']['L'][0]['M']['i']['L'][2]['L'][0]['N'];
								}
								foreach ($item_list as $key => $item){
									if (isset($alink1_tag)){
										if ($item->t == $alink1_tag){
											$alink1 = $item_list[$key]->n;
										}
									}
									if (isset($alink2_tag)){
										if ($item->t == $alink2_tag){
											$alink2 = $item_list[$key]->n;
										}
									}
									if (isset($alink3_tag)){
										if ($item->t == $alink3_tag){
											$alink3 = $item_list[$key]->n;
										}
									}
								} 
								if (!isset($alink1)){
									$alink1 = "";
								}
								if (!isset($alink2)){
									$alink1 = "";
								}
								if (!isset($alink3)){
									$alink1 = "";
								}
							} else {
								$aenhance = "";
								$aenhance_per = "";
								$alink1 = "";
								$alink2 = "";
								$alink3 = "";
							}
							if (!empty($value['links']['L'][1]['M'])){
								$renhance = $value['links']['L'][1]['M']['e']['S'];
								$renhance_per = $value['links']['L'][1]['M']['p']['N'];
								if (isset($value['links']['L'][1]['M']['i']['L'][0]['L'])){
									$rlink1_tag = $value['links']['L'][1]['M']['i']['L'][0]['L'][0]['N'];
								}
								if (isset($value['links']['L'][1]['M']['i']['L'][1]['L'])){
									$rlink2_tag = $value['links']['L'][1]['M']['i']['L'][1]['L'][0]['N'];
								}
								foreach ($item_list as $key => $item){
									if (isset($rlink1_tag)){
										if ($item->t == $rlink1_tag){
											$rlink1 = $item_list[$key]->n;
										}
									}
									if (isset($rlink2_tag)){
										if ($item->t == $rlink2_tag){
											$rlink2 = $item_list[$key]->n;
										}
									}
								}
								if (!isset($alink1)){
									$alink1 = "";
								}
								if (!isset($alink2)){
									$alink1 = "";
								}
							} else {
								$renhance = "";
								$renhance_per = "";
								$rlink1 = "";
								$rlink2 = "";
							}
							echo '<td>',$aenhance.'</td>';
							echo '<td>',$aenhance_per.'</td>';
							echo '<td>',$alink1.'</td>';
							echo '<td>',$alink2.'</td>';
							echo '<td>',$alink3.'</td>';
							echo '<td>',$renhance.'</td>';
							echo '<td>',$renhance_per.'</td>';
							echo '<td>',$rlink1.'</td>';
							echo '<td>',$rlink2.'</td>';
							echo "</tr>";
							$test = array_search($alink1_tag,$result['Items']);
							echo $test;
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
