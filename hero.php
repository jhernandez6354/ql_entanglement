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
			#include 'dynamodb.php';
			$token = $_SESSION['token'];
			$ch=curl_init();
			$curl_headers=array(
				"token: $token",
			);
			$uri='149.56.27.225';
			curl_setopt($ch,CURLOPT_URL,"http://$uri/client/init/");
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$get_hero = json_decode(curl_exec($ch), true);
			if(strpos($get_hero['status'], 'error') !== false){
				$uri='gs-global-wrk-04.api-ql.com';
				curl_setopt($ch,CURLOPT_URL,"http://$uri/client/init/");
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$get_hero = json_decode(curl_exec($ch), true);
			} 
			curl_close($ch);
			$player_id = $get_hero['data']['hero']['id'];
			$SESSION['player_id'] = $player_id;
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,"http://$uri/user/getprofile/?hero_id=".$player_id);
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
				#include 's3.php';
				#include 'dynamodb.php';
				$set_itemList[] =array();
				$item_list[] = array();
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android");
				$current_update = json_decode(curl_exec($ch));
				$wearable_sets = $current_update->data->static_data->crc_details->wearable_sets;
				$static_passive_skills = $current_update->data->static_data->crc_details->static_passive_skills;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$static_passive_skills/static_passive_skills/");
				$weaponPassive = json_decode(curl_exec($ch),true);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$wearable_sets/wearable_sets/");
				$setList = json_decode(curl_exec($ch),true);
				curl_close($ch);
				#$setList = json_decode(file_get_contents("./wearable_sets"),true);
				#$weaponPassive = json_decode(file_get_contents("./static_passive_skills"),true);
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
				#$s3Items = array();
				#$bucket = 'elasticbeanstalk-us-east-1-331694059185';
				#$s3objects = $s3Client->getIterator('ListObjects', array(
				#	'Bucket' => $bucket,
				#	'Prefix' => 'resources/storage/'
				#));
				#foreach ($s3objects as $object) {
				#	$s3Items[] = $object;
				#}
				$ch=curl_init();
				$curl_headers=array(
					"token: $token",
				);
				$uri='149.56.27.225';
				$player_id =$SESSION['player_id'];
				curl_setopt($ch,CURLOPT_URL,"http://$uri/user/getprofile/?hero_id=".$player_id);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$player_data = json_decode(curl_exec($ch), true);
				if(strpos($player_data['status'], 'error') !== false){
					$uri='gs-global-wrk-04.api-ql.com';
					curl_setopt($ch,CURLOPT_URL,"http://$uri/client/init/");
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$player_data = json_decode(curl_exec($ch), true);
				} 
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
					$element=null;
					$name=null;
					$slot=null;
					$quality=null;
					$indexed=null;
					$tvalue = $items['a'][1];
					foreach ($item_list as $key => $item){
						if (isset($tvalue)){
							if ($item->t == $tvalue){
								$ItemKey = $key;
								break;
							}
						}
					}
					if (isset($items["wear"])){ //Item is gear
						if($items["wear"][0] == 1){
							$indexed = "Equip";
							$image = $item_list[$ItemKey]->i_sd;
							$name = $item_list[$ItemKey]->n;
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($item_list[$ItemKey]->set == $set[0]) {
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
							if (isset($item_list[$ItemKey]->links[0])){
								$link1 = $items["wear"][3][0];
								$link2 = $items["wear"][3][1];
								$link3 = $items["wear"][3][2];
								$link4 = $items["wear"][3][3];
								$link5 = $items["wear"][3][4];
								
								$armor_link_enhance =  ucfirst($item_list[$ItemKey]->links[0]->e);
								$armor_link_bonus = $item_list[$ItemKey]->links[0]->p;
								$armor_link1_tag =  $item_list[$ItemKey]->links[0]->i[0][0];
								$armor_link2_tag =  $item_list[$ItemKey]->links[0]->i[1][0];
								$armor_link3_tag =  $item_list[$ItemKey]->links[0]->i[2][0];
								foreach ($item_list as $mykey => $myitem){
									if (isset($armor_link1_tag)){
										if ($myitem->t == $armor_link1_tag){
											$armor_link1_name = $item_list[$mykey]->n;
										}
									}
									if (isset($armor_link2_tag)){
										if ($myitem->t == $armor_link2_tag){
											$armor_link2_name = $item_list[$mykey]->n;
										}
									}
									if (isset($armor_link3_tag)){
										if ($myitem->t == $armor_link3_tag){
											$armor_link3_name = $item_list[$mykey]->n;
										}
									}
								}
								$orb_link_enhance =  ucfirst($item_list[$ItemKey]->links[1]->e);
								$orb_link_bonus = $item_list[$ItemKey]->links[1]->p;
								
								$orb_link1_tag =  $item_list[$ItemKey]->links[1]->i[0][0];
								$orb_link2_tag =  $item_list[$ItemKey]->links[1]->i[1][0];
								foreach ($item_list as $mykey => $myitem){
									if (isset($orb_link1_tag)){
										if ($myitem->t == $orb_link1_tag){
											$orb_link1_name = $item_list[$mykey]->n;
										}
									}
									if (isset($orb_link2_tag)){
										if ($myitem->t == $orb_link2_tag){
											$orb_link2_name = $item_list[$mykey]->n;
										}
									}
								} 
							}
							if (isset($item_list[$ItemKey]->pskls)){
								$db_passive1_effect = $item_list[$ItemKey]->pskls[0];
								$db_passive2_effect = $item_list[$ItemKey]->pskls[1];
							}
							#foreach ($s3Items as $object) {
							#	if (strpos($object['Key'], strval($image)) !== false) {
							#		$s3key = $object['Key'];
							#		break;
							#	}
							#}
							echo "<div class=\"$slot"."_txt\">$name</div>";
							echo "<div class=\"$slot\">";
								echo "<div class=\"equiphover\"><img id=\"$slot"."_img\" src=\"https://s3.amazonaws.com/$bucket/$s3key\" width=\"50\" height=\"50\"></img>";
								
								if (isset($item_list[$ItemKey]->links[0])){
									echo "<span class=\"tooltip\">
										<div class=\"a_enhance\">Armor Enhancement: $armor_link_enhance $armor_link_bonus%</div>
										<div class=\"a_link1\">$armor_link1_name</div>
										<div class=\"a_link2\">$armor_link2_name</div>
										<div class=\"a_link3\">$armor_link3_name</div>
										<div class=\"o_enhance\">Orb Enhancement: $armor_link_enhance $armor_link_bonus%</div>
										<div class=\"o_link1\">$orb_link1_name</div>
										<div class=\"o_link2\">$orb_link2_name</div>
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
							$name = $item_list[$ItemKey]->n;
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($item_list[$ItemKey]->set == $set[0]) {
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
							$name = $item_list[$ItemKey]->n;
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($item_list[$ItemKey]->set == $set[0]) {
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
							$name = $item_list[$ItemKey]->n;
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							if ($quality != "Common"){
								if ($quality != "Uncommon"){
									foreach ($setList as $set){
										if ($item_list[$ItemKey]->set == $set[0]) {
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
							$name = $item_list[$ItemKey]->n;
							$element = "NA";
							$slot = ucfirst($items["orb"][0]);
							$quality = ucfirst($item_list[$ItemKey]->q);
							$upgrade = $items["orb"][1];
							$boost = $items["orb"][2];
							$parts = 1;
						}else {
							$indexed = "Orb";
							$name = $item_list[$ItemKey]->n;
							$element = "NA";
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							$upgrade = $items["orb"][1];
							$boost = $items["orb"][2];
							$parts = 1;
						}
					} else {
						if ($item_list[$ItemKey]->s == "exactshard") {
							$indexed = "Parts";
							$shardIndex = $item_list[$ItemKey]->ritem[0];
							foreach ($item_list as $mykey => $myitem){
								if ($myitem->t == $shardIndex){
									$slot = ucfirst($item_list[$mykey]->s);
									foreach ($setList as $set){
										if ($item_list[$mykey]->set == $set[0]) {
											$element = $set[1];
										}
									}
									if (!isset($element)) {
										$element = "NA";
									}
								}
							}
							$name = $item_list[$ItemKey]->n;
							
							$quality = ucfirst($item_list[$ItemKey]->q);
							$upgrade = 0;
							$boost = 0;
							$parts = number_format($items['a'][5]);
							
						} else {
							$indexed = "Special";
							$name = $item_list[$ItemKey]->n;
							$element = "NA";
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
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
