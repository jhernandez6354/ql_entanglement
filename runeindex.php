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
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android');
$current_update = json_decode(curl_exec($ch));
$wearable_sets = $current_update->data->static_data->crc_details->wearable_sets;
$set_itemlist = $current_update->data->static_data->crc_details->item_templates;
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
foreach ($itemList as $key => $value) {
	if($value->s == 'rune'){
		echo "<tr>";
		echo '<td>',$value->n.'</td>';
		echo '<td>',$value->q.'</td>';
		echo '<td>',$value->s.'</td>';
		if (!empty($value->stats->hp)){
			echo '<td>',$value->stats->hp[1].'</td>';
			$HealthStat = $value->stats->hp[0];
			$AttackStat = "";
			$DefenseStat = "";
			
		}
		elseif (!empty($value->stats->dmg)){
			echo '<td>',$value->stats->dmg[1].'</td>';
			$AttackStat = $value->stats->dmg[0];
			$DefenseStat = "";
			$HealthStat = "";
		}
		elseif (!empty($value->stats->def)){
			echo '<td>',$value->stats->def[1].'</td>';
			$DefenseStat = $value->stats->def[0];
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
?>
	
	</div>
	</div>
</body>
</html>
