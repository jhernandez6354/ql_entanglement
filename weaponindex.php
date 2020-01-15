<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Weapon Index"/>
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
				<h1>Weapon Index</h1>
			</div>
<!--_____________________________________Navigation____________________________________ -->
		<nav id="nav">
			<ul>
				<li class="active" ><a href="index.php">Home</a></li>
				<li><a href="hero.php">Hero</a></li>
				<li><a href="optimize.php">Optimize</a></li>
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
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android');
$current_update = json_decode(curl_exec($ch));
$wearable_sets = $current_update->data->static_data->crc_details->wearable_sets;
$static_passive_skills = $current_update->data->static_data->crc_details->static_passive_skills;
$set_itemlist = $current_update->data->static_data->crc_details->item_templates;
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$static_passive_skills/static_passive_skills/");
$weaponPassive = json_decode(curl_exec($ch),true);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$wearable_sets/wearable_sets/");
$setList = json_decode(curl_exec($ch),true);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$set_itemlist/item_templates/");
$itemList = json_decode(curl_exec($ch));
curl_close($ch);

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
		foreach ($itemList as $key => $value) { #Lets make an array from the itemList called $itemArray
			if ($value->s == 'off_hand' || $value->s == 'main_hand'){
				if (isset($value->pskls)){
					$potential = (int)$value->stats->hp[1] + (int)$value->stats->def[1] + (int)$value->stats->dmg[1] + (int)$value->stats->magic[1];
					if ($value->q != "common"){
						if ($value->q != "uncommon"){
							foreach ($setList as $set){
								if ($value->set == $set[0]) {
									$element = $set[1];
								}
							}
						}
					} 
					if (!isset($element)) {
						$element = "NA";
					}
					if (isset($value->pskls)){
						if (isSet($value->pskls[0])){
							$db_passive1_effect = $value->pskls[0];
						}else {
							$db_passive1_effect = "NA";
						}
						if (isSet($value->pskls[1])){
							$db_passive2_effect = $value->pskls[1];
						}else {
							$db_passive2_effect = "NA";
						}
						
					}
					if ($db_passive1_effect != "NA"){
						foreach ($weaponPassive as $key => $passive){
							if ($db_passive1_effect == $key) {
								$db_passive1_effect = $passive['d'];
							}
						};
					}
					if ($db_passive1_effect != "NA"){
						foreach ($weaponPassive as $key => $passive){
							if ($db_passive2_effect == $key) {
								$db_passive2_effect = $passive['d'];
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
					echo '<td>',$value->n.'</td>';
					echo '<td>',$element.'</td>';
					echo '<td>',ucfirst($value->q).'</td>';
					echo '<td>',ucfirst($value->s).'</td>';
					echo '<td>',$potential.'</td>';
					echo '<td>',$value->stats->hp[0].'</td>';
					echo '<td>',$value->stats->dmg[0].'</td>';
					echo '<td>',$value->stats->def[0].'</td>';
					echo '<td>',$value->stats->magic[0].'</td>';
					echo '<td>',$db_passive1_effect.'</td>';
					echo '<td>',$db_passive2_effect.'</td>';
					echo "</tr>";
				}
			}
		}
		echo '</table>';

?>
	</div>
	</div>
</body>
</html>
