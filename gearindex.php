<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Gear Index"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700italic,400,300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/style.css" />
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
$set_itemList[] =array();
$item_list[] = array();
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android');
$current_update = json_decode(curl_exec($ch));
$set_itemlist = $current_update->data->static_data->crc_details->item_templates;
$wearable_sets = $current_update->data->static_data->crc_details->wearable_sets;
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$wearable_sets/wearable_sets/");
$setList = json_decode(curl_exec($ch));
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$set_itemlist/item_templates/");
$itemList = json_decode(curl_exec($ch));
curl_close($ch);
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
		foreach ($itemList as $key => $value) { #Lets make an array from the itemList called $itemArray
			if (!empty($value->stats->def)){
				if ($value->s != 'off_hand'){
					if($value->s != 'main_hand'){
						if($value->s != 'rune'){
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
							$potential = (int)$value->stats->hp[1] + (int)$value->stats->def[1] + (int)$value->stats->dmg[1] + (int)$value->stats->magic[1];
							echo "<tr>";
							echo '<td>',$value->n.'</td>';
							echo '<td>',$element.'</td>';
							echo '<td>',ucfirst($value->q).'</td>';
							echo '<td>',ucfirst($value->s).'</td>';
							echo '<td>',$potential.'</td>';
							echo '<td>',$value->stats->hp[0].'</td>';
							echo '<td>',$value->stats->hp[1].'</td>';
							echo '<td>',$value->stats->dmg[0].'</td>';
							echo '<td>',$value->stats->dmg[1].'</td>';
							echo '<td>',$value->stats->def[0].'</td>';
							echo '<td>',$value->stats->def[1].'</td>';
							echo '<td>',$value->stats->magic[0].'</td>';
							echo '<td>',$value->stats->magic[1].'</td>';
							if (!empty($value->links[0])){
								$aenhance = $value->links[0]->e;
								$aenhance_per = $value->links[0]->p;
								if (isset($value->links[0]->i[0])){
									$alink1_tag = $value->links[0]->i[0][0];
								}
								if (isset($value->links[0]->i[1])){
									$alink2_tag = $value->links[0]->i[1][0];
								}
								if (isset($value->links[0]->i[2])){
									$alink3_tag = $value->links[0]->i[2][0];
								}
								foreach ($itemList as $linkkey => $item){
									if (isset($alink1_tag)){
										if ($item->t == $alink1_tag){
											$alink1 = $item->n;
										}
									}
									if (isset($alink2_tag)){
										if ($item->t == $alink2_tag){
											$alink2 = $item->n;
										}
									}
									if (isset($alink3_tag)){
										if ($item->t == $alink3_tag){
											$alink3 = $item->n;
										}
									}
								} 
								if (!isset($alink1)){
									$alink1 = "";
								}
								if (!isset($alink2)){
									$alink2 = "";
								}
								if (!isset($alink3)){
									$alink3 = "";
								}
							} else {
								$aenhance = "";
								$aenhance_per = "";
								$alink1 = "";
								$alink2 = "";
								$alink3 = "";
							}
							if (!empty($value->links[1])){
								$renhance = $value->links[1]->e;
								$renhance_per = $value->links[1]->p;
								if (isset($value->links[1]->i[0])){
									$rlink1_tag = $value->links[1]->i[0][0];
								}
								if (isset($value->links[1]->i[1])){
									$rlink2_tag = $value->links[1]->i[1][0];
								}
								foreach ($itemList as $key => $item){
									if (isset($rlink1_tag)){
										if ($item->t == $rlink1_tag){
											$rlink1 = $item->n;
										}
									}
									if (isset($rlink2_tag)){
										if ($item->t == $rlink2_tag){
											$rlink2 = $item->n;
										}
									}
								}
								if (!isset($alink1)){
									$alink1 = "";
								}
								if (!isset($alink2)){
									$alink2 = "";
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
							$renhance = NULL;
							$renhance_per = NULL;
							$rlink1 = NULL;
							$rlink2 = NULL;
							$aenhance = NULL;
							$aenhance_per = NULL;
							$alink1 = NULL;
							$alink2 = NULL;
							$alink3 = NULL;
							$alink1_tag = NULL;
							$alink2_tag = NULL;
							$alink3_tag = NULL;
						}
					}
				}
			}
		}
		echo '</table>';

	

?>
	</div>
	</div>
</body>
</html>
