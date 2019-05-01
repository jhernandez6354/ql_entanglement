<?php
if (!isset($_SESSION)) {
	session_start();
	$_SESSION['redirect'] = "optimize.php";
}
require_once('header.php')
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Hero Manager</title>
	<meta name="description" content="Questland Hero Manager" />
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
					<li class="active"><a href="index.php">Home</a></li>
					<li><a href="hero.php">Hero</a></li>
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
			include 'dynamodb.php';
			$token = $_SESSION['token'];
			$ch = curl_init();
			$curl_headers = array(
				"token: $token",
			);
			curl_setopt($ch, CURLOPT_URL, 'http://149.56.27.225/client/init/');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$get_hero = json_decode(curl_exec($ch), true);
			curl_close($ch);
			$player_id = $get_hero['data']['hero']['id'];
			$SESSION['player_id'] = $player_id;
			?>
		</div>
		<div class="opti_grid">
			<?php
			if (isset($_SESSION['token'])) {
				error_reporting(0);
				$token = $_SESSION['token'];
				include 's3.php';
				include 'dynamodb.php';
				#These zeroed values will be used to define the collection array.
				$t_def_1 = 0;
				$w_def_1 = 0;
				$m_def_1 = 0;
				$t_dmg_1 = 0;
				$w_dmg_1 = 0;
				$m_dmg_1 = 0;
				$t_m_1 = 0;
				$w_m_1 = 0;
				$m_m_1 = 0;
				$thp1=0;
				$whp1=0;
				$mhp1=0;

				$set_itemList[] = array();
				$item_list[] = array();
				$setList = json_decode(file_get_contents("./wearable_sets"), true);
				$weaponPassive = json_decode(file_get_contents("./static_passive_skills"), true);
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
				$col_array = array();
				$s3Items = array();
				$bucket = 'elasticbeanstalk-us-east-1-331694059185';
				$s3objects = $s3Client->getIterator('ListObjects', array(
					'Bucket' => $bucket,
					'Prefix' => 'resources/storage/'
				));
				foreach ($s3objects as $object) {
					$s3Items[] = $object;
				}
				$ch = curl_init();
				$curl_headers = array(
					"token: $token",
				);
				$myitem_list = array();
				$myitem_list = $get_hero['data']['items_list'];
				$player_id = $SESSION['player_id'];
				curl_setopt($ch, CURLOPT_URL, 'http://149.56.27.225/user/getprofile/?hero_id=' . $player_id);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$player_data = json_decode(curl_exec($ch), true);
				curl_close($ch);

				foreach ($get_hero['data']['items_list'] as $items) {
					$t_collection_pick1_name=NULL;
					$w_collection_pick1_name=NULL;
					$m_collection_pick1_name=NULL;
					$t_collection_pick2_name=NULL;
					$w_collection_pick2_name=NULL;
					$m_collection_pick2_name=NULL;
					$armor_link1_boost=NULL;
					$armor_link1_enhance=NULL;
					$armor_link2_boost=NULL;
					$armor_link2_enhance=NULL;
					$armor_link3_boost=NULL;
					$armor_link3_enhance=NULL;
					$tvalue = $items['a'][1];
					foreach ($item_list as $key => $item) {
						if (isset($tvalue)) {
							if ($item->t == $tvalue) {
								$ItemKey = $key;
								break;
							}
						}
					}
					if (isset($items["wear"])) { //Item is gear
						if ($items["wear"][0] == 1) {
							$indexed = $item_list[$ItemKey]->t;
							$image = $item_list[$ItemKey]->i_sd;
							$name = $item_list[$ItemKey]->n;
							$slot = ucfirst($item_list[$ItemKey]->s);
							$quality = ucfirst($item_list[$ItemKey]->q);
							if (isset($quality)) {
								if ($quality == 'Legendary') {
									if (isset($item_list[$ItemKey]->links)) {
										$potential = $item_list[$ItemKey]->stats->def[1] + $item_list[$ItemKey]->stats->hp[1] + $item_list[$ItemKey]->stats->dmg[1] + $item_list[$ItemKey]->stats->magic[1];
										$boost = $items["wear"][2];
										$armor_link_enhance =  ucfirst($item_list[$ItemKey]->links[0]->e);
										$armor_link_bonus = $item_list[$ItemKey]->links[0]->p;
										$orb_link_enhance =  ucfirst($item_list[$ItemKey]->links[1]->e);
										$orb_link_bonus = $item_list[$ItemKey]->links[1]->p;
										$hp =  $item_list[$ItemKey]->stats->hp[1];
										$def =  $item_list[$ItemKey]->stats->def[1];
										$dmg =  $item_list[$ItemKey]->stats->dmg[1];
										$magic =  $item_list[$ItemKey]->stats->magic[1];
										$armor_link1_tag =  $item_list[$ItemKey]->links[0]->i[0][0];
										$armor_link2_tag =  $item_list[$ItemKey]->links[0]->i[1][0];
										$armor_link3_tag =  $item_list[$ItemKey]->links[0]->i[2][0];
										foreach ($item_list as $mykey => $myitem) { #Loop through database to find armor links.
											if (isset($armor_link1_tag)) {
												if ($myitem->t == $armor_link1_tag) { #If you find the item, report the name and enhancement.
													$armor_link1_name = $myitem->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link1_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link1_enhance =  ucfirst($myitem->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) { #Loop through my items to see if its already assembled.
														if ($mylistitem['a'][1] == $armor_link1_tag) {
															$armor_link1_boost = $mylistitem['wear'][2];
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link1_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link1_boost = 0;
															}
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
												}
												
											}
											
											if (isset($armor_link2_tag)) {
												if ($myitem->t == $armor_link2_tag) {
													$armor_link2_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link2_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link2_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link2_tag) {
															$armor_link2_boost = $mylistitem['wear'][2];
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link2_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link2_boost = 0;
															}
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
													
												}
											}
											if (isset($armor_link3_tag)) {
												if ($myitem->t == $armor_link3_tag) {
													$armor_link3_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link3_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link3_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link3_tag) {
															$armor_link3_boost = $mylistitem['wear'][2];
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link3_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link3_boost = 0;
															}
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
												}
											}
										}
									}
								}
							}
						} elseif ($items["wear"][0] == 2) {
							$quality = ucfirst($item_list[$ItemKey]->q);
							if (isset($quality)) {
								if ($quality == 'Legendary') {
									if (isset($item_list[$ItemKey]->links)) {
										$indexed = $item_list[$ItemKey]->t;
										$name = $item_list[$ItemKey]->n;
										$slot = ucfirst($item_list[$ItemKey]->s);
										$potential = $item_list[$ItemKey]->stats->def[1] + $item_list[$ItemKey]->stats->hp[1] + $item_list[$ItemKey]->stats->dmg[1] + $item_list[$ItemKey]->stats->magic[1];
										$boost = $items["wear"][2];
										$armor_link_enhance =  ucfirst($item_list[$ItemKey]->links[0]->e);
										$armor_link_bonus = $item_list[$ItemKey]->links[0]->p;
										$orb_link_enhance =  ucfirst($item_list[$ItemKey]->links[1]->e);
										$orb_link_bonus = $item_list[$ItemKey]->links[1]->p;
										$hp =  $item_list[$ItemKey]->stats->hp[1];
										$def =  $item_list[$ItemKey]->stats->def[1];
										$dmg =  $item_list[$ItemKey]->stats->dmg[1];
										$magic =  $item_list[$ItemKey]->stats->magic[1];
										$armor_link1_tag =  $item_list[$ItemKey]->links[0]->i[0][0];
										$armor_link2_tag =  $item_list[$ItemKey]->links[0]->i[1][0];
										$armor_link3_tag =  $item_list[$ItemKey]->links[0]->i[2][0];
										foreach ($item_list as $mykey => $myitem) { #Loop through database to find armor links.
											if (isset($armor_link1_tag)) {
												if ($myitem->t == $armor_link1_tag) { #If you find the item, report the name and enhancement.
													$armor_link1_name = $myitem->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link1_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link1_enhance =  ucfirst($myitem->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) { #Loop through my items to see if its already assembled.
														if ($mylistitem['a'][1] == $armor_link1_tag) {
															$armor_link1_boost = $mylistitem['wear'][2];
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link1_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link1_boost = 0;
															}
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
												}
												
											}
											
											if (isset($armor_link2_tag)) {
												if ($myitem->t == $armor_link2_tag) {
													$armor_link2_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link2_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link2_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link2_tag) {
															$armor_link2_boost = $mylistitem['wear'][2];
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link2_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link2_boost = 0;
															}
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
													
												}
											}
											if (isset($armor_link3_tag)) {
												if ($myitem->t == $armor_link3_tag) {
													$armor_link3_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link3_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link3_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link3_tag) {
															$armor_link3_boost = $mylistitem['wear'][2];
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link3_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link3_boost = 0;
															}
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
												}
											}
										}
									}
								}
							}
						} elseif ($items["wear"][0] == 3) {
							$quality = ucfirst($item_list[$ItemKey]->q);
							if (isset($quality)) {
								if ($quality == 'Legendary') {
									if (isset($item_list[$ItemKey]->links)) {
										$indexed = $item_list[$ItemKey]->t;
										$name = $item_list[$ItemKey]->n;
										$slot = ucfirst($item_list[$ItemKey]->s);
										$potential = $item_list[$ItemKey]->stats->def[1] + $item_list[$ItemKey]->stats->hp[1] + $item_list[$ItemKey]->stats->dmg[1] + $item_list[$ItemKey]->stats->magic[1];
										$boost = $items["wear"][2];
										$armor_link_enhance =  ucfirst($item_list[$ItemKey]->links[0]->e);
										$armor_link_bonus = $item_list[$ItemKey]->links[0]->p;
										$orb_link_enhance =  ucfirst($item_list[$ItemKey]->links[1]->e);
										$orb_link_bonus = $item_list[$ItemKey]->links[1]->p;
										$hp =  $item_list[$ItemKey]->stats->hp[1];
										$def =  $item_list[$ItemKey]->stats->def[1];
										$dmg =  $item_list[$ItemKey]->stats->dmg[1];
										$magic =  $item_list[$ItemKey]->stats->magic[1];
										$armor_link1_tag =  $item_list[$ItemKey]->links[0]->i[0][0];
										$armor_link2_tag =  $item_list[$ItemKey]->links[0]->i[1][0];
										$armor_link3_tag =  $item_list[$ItemKey]->links[0]->i[2][0];
										foreach ($item_list as $mykey => $myitem) { #Loop through database to find armor links.
											if (isset($armor_link1_tag)) {
												if ($myitem->t == $armor_link1_tag) { #If you find the item, report the name and enhancement.
													$armor_link1_name = $myitem->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link1_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link1_enhance =  ucfirst($myitem->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) { #Loop through my items to see if its already assembled.
														if ($mylistitem['a'][1] == $armor_link1_tag) {
															$armor_link1_boost = $mylistitem['wear'][2];
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link1_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link1_boost = 0;
															}
															$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link1_def =  $item_list[$mykey]->stats->def[1];
															$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
												}
												
											}
											
											if (isset($armor_link2_tag)) {
												if ($myitem->t == $armor_link2_tag) {
													$armor_link2_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link2_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link2_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link2_tag) {
															$armor_link2_boost = $mylistitem['wear'][2];
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link2_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link2_boost = 0;
															}
															$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link2_def =  $item_list[$mykey]->stats->def[1];
															$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
													
												}
											}
											if (isset($armor_link3_tag)) {
												if ($myitem->t == $armor_link3_tag) {
													$armor_link3_name = $item_list[$mykey]->n;
													foreach ($item_list as $myshardkey => $mysharditem){
														if ($mysharditem->n == $armor_link3_name) {
															if (isset($mysharditem->ritem))
																$shardIndex = $mysharditem->t;
																continue;
														}
													}
													$armor_link3_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
													foreach ($myitem_list as $mylistkey => $mylistitem) {
														if ($mylistitem['a'][1] == $armor_link3_tag) {
															$armor_link3_boost = $mylistitem['wear'][2];
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
														} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
															$parts = $mylistitem['a'][5];
															if ($parts >= 30) {
																$armor_link3_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
															} else {
																$armor_link3_boost = 0;
															}
															$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
															$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
															$armor_link3_def =  $item_list[$mykey]->stats->def[1];
															$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
															$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
															continue;
														}
													}
													
												}
											}
										}
									}

									if ($armor_link1_boost > 0) {
										if ($armor_link2_boost > 0) {
											#Compare links 1 and 2 first
											if ($armor_link1_boost > $armor_link2_boost) {
												#Discover what the bonus is to determine where to put it.
												$link_boost_1 = $armor_link1_boost;
												$link_boost_2 = $armor_link2_boost;
												if ($armor_link1_enhance == 'Defence') {
													$t_collection_pick1_name = $armor_link1_name;
													$t_collection_pick2_name = $armor_link2_name;
												}
												if ($armor_link1_enhance == 'Damage') {
													$w_collection_pick1_name = $armor_link1_name;
													$w_collection_pick2_name = $armor_link2_name;
												}
												if ($armor_link1_enhance == 'Magic') {
													$m_collection_pick1_name = $armor_link1_name;
													$m_collection_pick2_name = $armor_link2_name;
												}
											} else {
												$link_boost_1 = $armor_link2_boost;
												$link_boost_2 = $armor_link1_boost;
												if ($armor_link2_enhance == 'Defence') {
													$t_collection_pick1_name = $armor_link2_name;
													$t_collection_pick2_name = $armor_link1_name;
												}
												if ($armor_link2_enhance == 'Damage') {
													$w_collection_pick1_name = $armor_link2_name;
													$w_collection_pick2_name = $armor_link1_name;
												}
												if ($armor_link2_enhance == 'Magic') {
													$m_collection_pick1_name = $armor_link2_name;
													$m_collection_pick2_name = $armor_link1_name;
												}
											}
										}
									} elseif ($armor_link2_boost > 0) {
										$link_boost_1 = $armor_link2_boost;
										if ($armor_link2_enhance == 'Defence') {
											$t_collection_pick1_name = $armor_link2_name;
										}
										if ($armor_link2_enhance == 'Damage') {
											$w_collection_pick1_name = $armor_link2_name;
										}
										if ($armor_link2_enhance == 'Magic') {
											$m_collection_pick1_name = $armor_link2_name;
										}
									}
									if ($armor_link3_boost > 0) {
										if (isset($link_boost_1)) {
											if (isset($link_boost_1)) {
												if ($armor_link3_boost > $link_boost_1) { #Armor link 3 is the best piece
													if ($armor_link3_enhance == 'Defence') {
														$t_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Damage') {
														$w_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Magic') {
														$m_collection_pick1_name = $armor_link3_name;
													}
												} elseif (isset($link_boost_2)) {
													if ($armor_link3_boost > $link_boost_2) { #Armor link 3 is the second best.
														if ($armor_link3_enhance == 'Defence') {
															$t_collection_pick2_name = $armor_link3_name;
														}
														if ($armor_link3_enhance == 'Damage') {
															$w_collection_pick2_name = $armor_link3_name;
														}
														if ($armor_link3_enhance == 'Magic') {
															$m_collection_pick2_name = $armor_link3_name;
														}
													}
												} else { #Armor Link 2 is not set, making armor link 3 the second best.
													if ($armor_link3_enhance == 'Defence') {
														$t_collection_pick2_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Damage') {
														$w_collection_pick2_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Magic') {
														$m_collection_pick2_name = $armor_link3_name;
													}
												}
											} else {
												if (isset($link_boost_2)) { #Link 2 does not exist, making this the second best.
													if ($armor_link3_enhance == 'Defence') {
														$t_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Damage') {
														$w_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Magic') {
														$m_collection_pick1_name = $armor_link3_name;
													}
												}
											}
										} else { #Link 1 and 2 were not set, making this the best option.
											if ($armor_link3_enhance == 'Defence') {
												$t_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Damage') {
												$w_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Magic') {
												$m_collection_pick1_name = $armor_link3_name;
											}
										}
									}
								}
							}
						} else {
							$quality = ucfirst($item_list[$ItemKey]->q);
							if (isset($quality)) {
								if ($quality == 'Legendary') {
									if ($item_list[$ItemKey]->s != "exactshard") {
										if (isset($item_list[$ItemKey]->links)) {
											$indexed = $item_list[$ItemKey]->t;
											$name = $item_list[$ItemKey]->n;
											$slot = ucfirst($item_list[$ItemKey]->s);
											$potential = $item_list[$ItemKey]->stats->def[1] + $item_list[$ItemKey]->stats->hp[1] + $item_list[$ItemKey]->stats->dmg[1] + $item_list[$ItemKey]->stats->magic[1];
											$boost = $items["wear"][2];
											$armor_link_enhance =  ucfirst($item_list[$ItemKey]->links[0]->e);
											$armor_link_bonus = $item_list[$ItemKey]->links[0]->p;
											$orb_link_enhance =  ucfirst($item_list[$ItemKey]->links[1]->e);
											$orb_link_bonus = $item_list[$ItemKey]->links[1]->p;
											$hp =  $item_list[$ItemKey]->stats->hp[1];
											$def =  $item_list[$ItemKey]->stats->def[1];
											$dmg =  $item_list[$ItemKey]->stats->dmg[1];
											$magic =  $item_list[$ItemKey]->stats->magic[1];
											$armor_link1_tag =  $item_list[$ItemKey]->links[0]->i[0][0];
											$armor_link2_tag =  $item_list[$ItemKey]->links[0]->i[1][0];
											$armor_link3_tag =  $item_list[$ItemKey]->links[0]->i[2][0];
											foreach ($item_list as $mykey => $myitem) { #Loop through database to find armor links.
												if (isset($armor_link1_tag)) {
													if ($myitem->t == $armor_link1_tag) { #If you find the item, report the name and enhancement.
														$armor_link1_name = $myitem->n;
														foreach ($item_list as $myshardkey => $mysharditem){
															if ($mysharditem->n == $armor_link1_name) {
																if (isset($mysharditem->ritem))
																	$shardIndex = $mysharditem->t;
																	continue;
															}
														}
														$armor_link1_enhance =  ucfirst($myitem->links[0]->e);
														foreach ($myitem_list as $mylistkey => $mylistitem) { #Loop through my items to see if its already assembled.
															if ($mylistitem['a'][1] == $armor_link1_tag) {
																$armor_link1_boost = $mylistitem['wear'][2];
																$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link1_def =  $item_list[$mykey]->stats->def[1];
																$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
																continue;
															} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
																$parts = $mylistitem['a'][5];
																if ($parts >= 30) {
																	$armor_link1_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
																} else {
																	$armor_link1_boost = 0;
																}
																$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link1_def =  $item_list[$mykey]->stats->def[1];
																$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
																continue;
															}
														}
													}
													
												}
												
												if (isset($armor_link2_tag)) {
													if ($myitem->t == $armor_link2_tag) {
														$armor_link2_name = $item_list[$mykey]->n;
														foreach ($item_list as $myshardkey => $mysharditem){
															if ($mysharditem->n == $armor_link2_name) {
																if (isset($mysharditem->ritem))
																	$shardIndex = $mysharditem->t;
																	continue;
															}
														}
														$armor_link2_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
														foreach ($myitem_list as $mylistkey => $mylistitem) {
															if ($mylistitem['a'][1] == $armor_link2_tag) {
																$armor_link2_boost = $mylistitem['wear'][2];
																$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link2_def =  $item_list[$mykey]->stats->def[1];
																$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
																continue;
															} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
																$parts = $mylistitem['a'][5];
																if ($parts >= 30) {
																	$armor_link2_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
																} else {
																	$armor_link2_boost = 0;
																}
																$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link2_def =  $item_list[$mykey]->stats->def[1];
																$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
																continue;
															}
														}
														
														
													}
												}
												if (isset($armor_link3_tag)) {
													if ($myitem->t == $armor_link3_tag) {
														$armor_link3_name = $item_list[$mykey]->n;
														foreach ($item_list as $myshardkey => $mysharditem){
															if ($mysharditem->n == $armor_link3_name) {
																if (isset($mysharditem->ritem))
																	$shardIndex = $mysharditem->t;
																	continue;
															}
														}
														$armor_link3_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
														foreach ($myitem_list as $mylistkey => $mylistitem) {
															if ($mylistitem['a'][1] == $armor_link3_tag) {
																$armor_link3_boost = $mylistitem['wear'][2];
																$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link3_def =  $item_list[$mykey]->stats->def[1];
																$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
															} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
																$parts = $mylistitem['a'][5];
																if ($parts >= 30) {
																	$armor_link3_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
																} else {
																	$armor_link3_boost = 0;
																}
																$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
																$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
																$armor_link3_def =  $item_list[$mykey]->stats->def[1];
																$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
																$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
																continue;
															}
														}
														
													}
												}
											}


											if ($armor_link1_boost > 0) {
												if ($armor_link2_boost > 0) {
													#Compare links 1 and 2 first
													if ($armor_link1_boost > $armor_link2_boost) {
														#Discover what the bonus is to determine where to put it.
														$link_boost_1 = $armor_link1_boost;
														$link_boost_2 = $armor_link2_boost;
														if ($armor_link1_enhance == 'Defence') {
															$t_collection_pick1_name = $armor_link1_name;
															$t_collection_pick2_name = $armor_link2_name;
														}
														if ($armor_link1_enhance == 'Damage') {
															$w_collection_pick1_name = $armor_link1_name;
															$w_collection_pick2_name = $armor_link2_name;
														}
														if ($armor_link1_enhance == 'Magic') {
															$m_collection_pick1_name = $armor_link1_name;
															$m_collection_pick2_name = $armor_link2_name;
														}
													} else {
														$link_boost_1 = $armor_link2_boost;
														$link_boost_2 = $armor_link1_boost;
														if ($armor_link2_enhance == 'Defence') {
															$t_collection_pick1_name = $armor_link2_name;
															$t_collection_pick2_name = $armor_link1_name;
														}
														if ($armor_link2_enhance == 'Damage') {
															$w_collection_pick1_name = $armor_link2_name;
															$w_collection_pick2_name = $armor_link1_name;
														}
														if ($armor_link2_enhance == 'Magic') {
															$m_collection_pick1_name = $armor_link2_name;
															$m_collection_pick2_name = $armor_link1_name;
														}
													}
												}
											} elseif ($armor_link2_boost > 0) {
												$link_boost_1 = $armor_link2_boost;
												if ($armor_link2_enhance == 'Defence') {
													$t_collection_pick1_name = $armor_link2_name;
												}
												if ($armor_link2_enhance == 'Damage') {
													$w_collection_pick1_name = $armor_link2_name;
												}
												if ($armor_link2_enhance == 'Magic') {
													$m_collection_pick1_name = $armor_link2_name;
												}
											}
											if ($armor_link3_boost > 0) {
												if (isset($link_boost_1)) {
													if (isset($link_boost_1)) {
														if ($armor_link3_boost > $link_boost_1) { #Armor link 3 is the best piece
															if ($armor_link3_enhance == 'Defence') {
																$t_collection_pick1_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Damage') {
																$w_collection_pick1_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Magic') {
																$m_collection_pick1_name = $armor_link3_name;
															}
														} elseif (isset($link_boost_2)) {
															if ($armor_link3_boost > $link_boost_2) { #Armor link 3 is the second best.
																if ($armor_link3_enhance == 'Defence') {
																	$t_collection_pick2_name = $armor_link3_name;
																}
																if ($armor_link3_enhance == 'Damage') {
																	$w_collection_pick2_name = $armor_link3_name;
																}
																if ($armor_link3_enhance == 'Magic') {
																	$m_collection_pick2_name = $armor_link3_name;
																}
															}
														} else { #Armor Link 2 is not set, making armor link 3 the second best.
															if ($armor_link3_enhance == 'Defence') {
																$t_collection_pick2_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Damage') {
																$w_collection_pick2_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Magic') {
																$m_collection_pick2_name = $armor_link3_name;
															}
														}
													} else {
														if (isset($link_boost_2)) { #Link 2 does not exist, making this the second best.
															if ($armor_link3_enhance == 'Defence') {
																$t_collection_pick1_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Damage') {
																$w_collection_pick1_name = $armor_link3_name;
															}
															if ($armor_link3_enhance == 'Magic') {
																$m_collection_pick1_name = $armor_link3_name;
															}
														}
													}
												} else { #Link 1 and 2 were not set, making this the best option.
													if ($armor_link3_enhance == 'Defence') {
														$t_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Damage') {
														$w_collection_pick1_name = $armor_link3_name;
													}
													if ($armor_link3_enhance == 'Magic') {
														$m_collection_pick1_name = $armor_link3_name;
													}
												}
											}
										}
									}
								}
							}
						}
					} else { //Item is in parts
						$parts = $items['a'][5];
						if ($parts > 29) {
							
							if ($parts >= 30) {
								$boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
							} else {
								$boost = 0;
							}
							$indexed = $item_list[$ItemKey]->ritem[0];
							$name = $item_list[$ItemKey]->n;
							foreach ($item_list as $mybasekey => $mybaseitem) {
								if ($mybaseitem->t == $indexed) {
									$slot = ucfirst($item_list[$mybasekey]->s);
									$potential = $mybaseitem->stats->def[1] + $mybaseitem->stats->hp[1] + $mybaseitem->stats->dmg[1] + $mybaseitem->stats->magic[1];
									$armor_link_enhance =  ucfirst($mybaseitem->links[0]->e);
									$armor_link_bonus = $mybaseitem->links[0]->p;
									$orb_link_enhance =  ucfirst($mybaseitem->links[1]->e);
									$orb_link_bonus = $mybaseitem->links[1]->p;
									$hp = $mybaseitem->stats->hp[1];
									$def = $mybaseitem->stats->def[1];
									$dmg = $mybaseitem->stats->dmg[1];
									$magic = $mybaseitem->stats->magic[1];
									$armor_link1_tag =  $mybaseitem->links[0]->i[0][0];
									$armor_link2_tag =  $mybaseitem->links[0]->i[1][0];
									$armor_link3_tag =  $mybaseitem->links[0]->i[2][0];
									foreach ($item_list as $mykey => $myitem) { #Loop through database to find armor links.
										if (isset($armor_link1_tag)) {
											if ($myitem->t == $armor_link1_tag) { #If you find the item, report the name and enhancement.
												$armor_link1_name = $myitem->n;
												foreach ($item_list as $myshardkey => $mysharditem){
													if ($mysharditem->n == $armor_link1_name) {
														if (isset($mysharditem->ritem))
															$shardIndex = $mysharditem->t;
															continue;
													}
												}
												$armor_link1_enhance =  ucfirst($myitem->links[0]->e);
												foreach ($myitem_list as $mylistkey => $mylistitem) { #Loop through my items to see if its already assembled.
													if ($mylistitem['a'][1] == $armor_link1_tag) {
														$armor_link1_boost = $mylistitem['wear'][2];
														$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link1_def =  $item_list[$mykey]->stats->def[1];
														$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
														continue;
													} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
														$parts = $mylistitem['a'][5];
														if ($parts >= 30) {
															$armor_link1_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
														} else {
															$armor_link1_boost = 0;
														}
														$armor_link1_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link1_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link1_def =  $item_list[$mykey]->stats->def[1];
														$armor_link1_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link1_magic =  $item_list[$mykey]->stats->magic[1];
														continue;
													}
												}
											}
											
										}
										
										if (isset($armor_link2_tag)) {
											if ($myitem->t == $armor_link2_tag) {
												$armor_link2_name = $item_list[$mykey]->n;
												foreach ($item_list as $myshardkey => $mysharditem){
													if ($mysharditem->n == $armor_link2_name) {
														if (isset($mysharditem->ritem))
															$shardIndex = $mysharditem->t;
															continue;
													}
												}
												$armor_link2_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
												foreach ($myitem_list as $mylistkey => $mylistitem) {
													if ($mylistitem['a'][1] == $armor_link2_tag) {
														$armor_link2_boost = $mylistitem['wear'][2];
														$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link2_def =  $item_list[$mykey]->stats->def[1];
														$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
														continue;
													} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
														$parts = $mylistitem['a'][5];
														if ($parts >= 30) {
															$armor_link2_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
														} else {
															$armor_link2_boost = 0;
														}
														$armor_link2_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link2_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link2_def =  $item_list[$mykey]->stats->def[1];
														$armor_link2_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link2_magic =  $item_list[$mykey]->stats->magic[1];
														continue;
													}
												}
												
												
											}
										}
										if (isset($armor_link3_tag)) {
											if ($myitem->t == $armor_link3_tag) {
												$armor_link3_name = $item_list[$mykey]->n;
												foreach ($item_list as $myshardkey => $mysharditem){
													if ($mysharditem->n == $armor_link3_name) {
														if (isset($mysharditem->ritem))
															$shardIndex = $mysharditem->t;
															continue;
													}
												}
												$armor_link3_enhance =  ucfirst($item_list[$mykey]->links[0]->e);
												foreach ($myitem_list as $mylistkey => $mylistitem) {
													if ($mylistitem['a'][1] == $armor_link3_tag) {
														$armor_link3_boost = $mylistitem['wear'][2];
														$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link3_def =  $item_list[$mykey]->stats->def[1];
														$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
													} else if($mylistitem['a'][1] == $shardIndex) { #Next check if it is in parts.
														$parts = $mylistitem['a'][5];
														if ($parts >= 30) {
															$armor_link3_boost = floor($parts/30); #Always round down to the nearest integer to prevent it from thinking you have more boost than is available.
														} else {
															$armor_link3_boost = 0;
														}
														$armor_link3_bonus = $item_list[$mykey]->links[0]->p;
														$armor_link3_hp =  $item_list[$mykey]->stats->hp[1];
														$armor_link3_def =  $item_list[$mykey]->stats->def[1];
														$armor_link3_dmg =  $item_list[$mykey]->stats->dmg[1];
														$armor_link3_magic =  $item_list[$mykey]->stats->magic[1];
														continue;
													}
												}
												
											}
										}
									}
								}
							}
							if ($armor_link1_boost > 0) {
								if ($armor_link2_boost > 0) {
									#Compare links 1 and 2 first
									if ($armor_link1_boost > $armor_link2_boost) {
										#Discover what the bonus is to determine where to put it.
										$link_boost_1 = $armor_link1_boost;
										$link_boost_2 = $armor_link2_boost;
										if ($armor_link1_enhance == 'Defence') {
											$t_collection_pick1_name = $armor_link1_name;
											$t_collection_pick2_name = $armor_link2_name;
										}
										if ($armor_link1_enhance == 'Damage') {
											$w_collection_pick1_name = $armor_link1_name;
											$w_collection_pick2_name = $armor_link2_name;
										}
										if ($armor_link1_enhance == 'Magic') {
											$m_collection_pick1_name = $armor_link1_name;
											$m_collection_pick2_name = $armor_link2_name;
										}
									} else {
										$link_boost_1 = $armor_link2_boost;
										$link_boost_2 = $armor_link1_boost;
										if ($armor_link2_enhance == 'Defence') {
											$t_collection_pick1_name = $armor_link2_name;
											$t_collection_pick2_name = $armor_link1_name;
										}
										if ($armor_link2_enhance == 'Damage') {
											$w_collection_pick1_name = $armor_link2_name;
											$w_collection_pick2_name = $armor_link1_name;
										}
										if ($armor_link2_enhance == 'Magic') {
											$m_collection_pick1_name = $armor_link2_name;
											$m_collection_pick2_name = $armor_link1_name;
										}
									}
								}
							} elseif ($armor_link2_boost > 0) {
								$link_boost_1 = $armor_link2_boost;
								if ($armor_link2_enhance == 'Defence') {
									$t_collection_pick1_name = $armor_link2_name;
								}
								if ($armor_link2_enhance == 'Damage') {
									$w_collection_pick1_name = $armor_link2_name;
								}
								if ($armor_link2_enhance == 'Magic') {
									$m_collection_pick1_name = $armor_link2_name;
								}
							}
							if ($armor_link3_boost > 0) {
								if (isset($link_boost_1)) {
									if (isset($link_boost_1)) {
										if ($armor_link3_boost > $link_boost_1) { #Armor link 3 is the best piece
											if ($armor_link3_enhance == 'Defence') {
												$t_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Damage') {
												$w_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Magic') {
												$m_collection_pick1_name = $armor_link3_name;
											}
										} elseif (isset($link_boost_2)) {
											if ($armor_link3_boost > $link_boost_2) { #Armor link 3 is the second best.
												if ($armor_link3_enhance == 'Defence') {
													$t_collection_pick2_name = $armor_link3_name;
												}
												if ($armor_link3_enhance == 'Damage') {
													$w_collection_pick2_name = $armor_link3_name;
												}
												if ($armor_link3_enhance == 'Magic') {
													$m_collection_pick2_name = $armor_link3_name;
												}
											}
										} else { #Armor Link 2 is not set, making armor link 3 the second best.
											if ($armor_link3_enhance == 'Defence') {
												$t_collection_pick2_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Damage') {
												$w_collection_pick2_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Magic') {
												$m_collection_pick2_name = $armor_link3_name;
											}
										}
									} else {
										if (isset($link_boost_2)) { #Link 2 does not exist, making this the second best.
											if ($armor_link3_enhance == 'Defence') {
												$t_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Damage') {
												$w_collection_pick1_name = $armor_link3_name;
											}
											if ($armor_link3_enhance == 'Magic') {
												$m_collection_pick1_name = $armor_link3_name;
											}
										}
									}
								} else { #Link 1 and 2 were not set, making this the best option.
									if ($armor_link3_enhance == 'Defence') {
										$t_collection_pick1_name = $armor_link3_name;
									}
									if ($armor_link3_enhance == 'Damage') {
										$w_collection_pick1_name = $armor_link3_name;
									}
									if ($armor_link3_enhance == 'Magic') {
										$m_collection_pick1_name = $armor_link3_name;
									}
								}
							}
						}
					}
					#Section 1, First pick the primary gear we will equip.
					if (isset($quality)) {
						if ($quality == 'Legendary') { #Don't consider items that are not legendary or better
							$test = 0;
							if ($armor_link1_boost > 0) {
								$test = 1;
							} else {
								$test = 0;
							}
							if ($armor_link2_boost > 0) {
								$test += 1;
							}
							if ($armor_link2_boost > 0) {
								$test += 1;
							}
							if ($slot == 'Head') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_helm_def_pot)) { #This is the first time the item will be set.
										$t_helm_def_pot = $potential;
										$t_helm_def_index = $indexed;
										$t_helm_def_stat = $def;
										$t_helm_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_helm_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_helm_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_helm_def_pot = $potential;
													$t_helm_def_index = $indexed;
													$t_helm_def_stat = $def;
													$t_helm_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_helm_dmg_pot)) {
										$w_helm_dmg_pot = $potential;
										$w_helm_dmg_index = $indexed;
										$w_helm_dmg_stat = $dmg;
										$w_helm_dmg_name = $name;
									} else {
										if ($w_helm_dmg_pot < $potential) {
											if ($w_helm_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_helm_dmg_pot = $potential;
													$w_helm_dmg_index = $indexed;
													$w_helm_dmg_stat = $dmg;
													$w_helm_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_helm_magic_pot)) {
										$m_helm_magic_pot = $potential;
										$m_helm_magic_index = $indexed;
										$m_helm_magic_stat = $magic;
										$m_helm_magic_name = $name;
									} else {
										if ($m_helm_magic_pot < $potential) {
											if ($m_helm_magic_stat < $magic) {
												if ($test >= 2) {
													$m_helm_magic_pot = $potential;
													$m_helm_magic_index = $indexed;
													$m_helm_magic_stat = $magic;
													$m_helm_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Chest') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_chest_def_pot)) { #This is the first time the item will be set.
										$t_chest_def_pot = $potential;
										$t_chest_def_index = $indexed;
										$t_chest_def_stat = $def;
										$t_chest_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_chest_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_chest_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_chest_def_pot = $potential;
													$t_chest_def_index = $indexed;
													$t_chest_def_stat = $def;
													$t_chest_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_chest_dmg_pot)) {
										$w_chest_dmg_pot = $potential;
										$w_chest_dmg_index = $indexed;
										$w_chest_dmg_stat = $dmg;
										$w_chest_dmg_name = $name;
									} else {
										if ($w_chest_dmg_pot < $potential) {
											if ($w_chest_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_chest_dmg_pot = $potential;
													$w_chest_dmg_index = $indexed;
													$w_chest_dmg_stat = $dmg;
													$w_chest_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_chest_magic_pot)) {
										$m_chest_magic_pot = $potential;
										$m_chest_magic_index = $indexed;
										$m_chest_magic_stat = $magic;
										$m_chest_magic_name = $name;
									} else {
										if ($m_chest_magic_pot < $potential) {
											if ($m_chest_magic_stat < $magic) {
												if ($test >= 2) {
													$m_chest_magic_pot = $potential;
													$m_chest_magic_index = $indexed;
													$m_chest_magic_stat = $magic;
													$m_chest_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Feet') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_feet_def_pot)) { #This is the first time the item will be set.
										$t_feet_def_pot = $potential;
										$t_feet_def_index = $indexed;
										$t_feet_def_stat = $def;
										$t_feet_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_feet_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_feet_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_feet_def_pot = $potential;
													$t_feet_def_index = $indexed;
													$t_feet_def_stat = $def;
													$t_feet_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_feet_dmg_pot)) {
										$w_feet_dmg_pot = $potential;
										$w_feet_dmg_index = $indexed;
										$w_feet_dmg_stat = $dmg;
										$w_feet_dmg_name = $name;
									} else {
										if ($w_feet_dmg_pot < $potential) {
											if ($w_feet_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_feet_dmg_pot = $potential;
													$w_feet_dmg_index = $indexed;
													$w_feet_dmg_stat = $dmg;
													$w_feet_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_feet_magic_pot)) {
										$m_feet_magic_pot = $potential;
										$m_feet_magic_index = $indexed;
										$m_feet_magic_stat = $magic;
										$m_feet_magic_name = $name;
									} else {
										if ($m_feet_magic_pot < $potential) {
											if ($m_feet_magic_stat < $magic) {
												if ($test >= 2) {
													$m_feet_magic_pot = $potential;
													$m_feet_magic_index = $indexed;
													$m_feet_magic_stat = $magic;
													$m_feet_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Gloves') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_gloves_def_pot)) { #This is the first time the item will be set.
										$t_gloves_def_pot = $potential;
										$t_gloves_def_index = $indexed;
										$t_gloves_def_stat = $def;
										$t_gloves_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_gloves_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_gloves_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_gloves_def_pot = $potential;
													$t_gloves_def_index = $indexed;
													$t_gloves_def_stat = $def;
													$t_gloves_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_gloves_dmg_pot)) {
										$w_gloves_dmg_pot = $potential;
										$w_gloves_dmg_index = $indexed;
										$w_gloves_dmg_stat = $dmg;
										$w_gloves_dmg_name = $name;
									} else {
										if ($w_gloves_dmg_pot < $potential) {
											if ($w_gloves_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_gloves_dmg_pot = $potential;
													$w_gloves_dmg_index = $indexed;
													$w_gloves_dmg_stat = $dmg;
													$w_gloves_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_gloves_magic_pot)) {
										$m_gloves_magic_pot = $potential;
										$m_gloves_magic_index = $indexed;
										$m_gloves_magic_stat = $magic;
										$m_gloves_magic_name = $name;
									} else {
										if ($m_gloves_magic_pot < $potential) {
											if ($m_gloves_magic_stat < $magic) {
												if ($test >= 2) {
													$m_gloves_magic_pot = $potential;
													$m_gloves_magic_index = $indexed;
													$m_gloves_magic_stat = $magic;
													$m_gloves_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Ring') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_ring_def_pot)) { #This is the first time the item will be set.
										$t_ring_def_pot = $potential;
										$t_ring_def_index = $indexed;
										$t_ring_def_stat = $def;
										$t_ring_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_ring_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_ring_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_ring_def_pot = $potential;
													$t_ring_def_index = $indexed;
													$t_ring_def_stat = $def;
													$t_ring_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_ring_dmg_pot)) {
										$w_ring_dmg_pot = $potential;
										$w_ring_dmg_index = $indexed;
										$w_ring_dmg_stat = $dmg;
										$w_ring_dmg_name = $name;
									} else {
										if ($w_ring_dmg_pot < $potential) {
											if ($w_ring_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_ring_dmg_pot = $potential;
													$w_ring_dmg_index = $indexed;
													$w_ring_dmg_stat = $dmg;
													$w_ring_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_ring_magic_pot)) {
										$m_ring_magic_pot = $potential;
										$m_ring_magic_index = $indexed;
										$m_ring_magic_stat = $magic;
										$m_ring_magic_name = $name;
									} else {
										if ($m_ring_magic_pot < $potential) {
											if ($m_ring_magic_stat < $magic) {
												if ($test >= 2) {
													$m_ring_magic_pot = $potential;
													$m_ring_magic_index = $indexed;
													$m_ring_magic_stat = $magic;
													$m_ring_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Amulet') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_amulet_def_pot)) { #This is the first time the item will be set.
										$t_amulet_def_pot = $potential;
										$t_amulet_def_index = $indexed;
										$t_amulet_def_stat = $def;
										$t_amulet_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_amulet_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_amulet_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_amulet_def_pot = $potential;
													$t_amulet_def_index = $indexed;
													$t_amulet_def_stat = $def;
													$t_amulet_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_amulet_dmg_pot)) {
										$w_amulet_dmg_pot = $potential;
										$w_amulet_dmg_index = $indexed;
										$w_amulet_dmg_stat = $dmg;
										$w_amulet_dmg_name = $name;
									} else {
										if ($w_amulet_dmg_pot < $potential) {
											if ($w_amulet_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_amulet_dmg_pot = $potential;
													$w_amulet_dmg_index = $indexed;
													$w_amulet_dmg_stat = $dmg;
													$w_amulet_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_amulet_magic_pot)) {
										$m_amulet_magic_pot = $potential;
										$m_amulet_magic_index = $indexed;
										$m_amulet_magic_stat = $magic;
										$m_amulet_magic_name = $name;
									} else {
										if ($m_amulet_magic_pot < $potential) {
											if ($m_amulet_magic_stat < $magic) {
												if ($test >= 2) {
													$m_amulet_magic_pot = $potential;
													$m_amulet_magic_index = $indexed;
													$m_amulet_magic_stat = $magic;
													$m_amulet_magic_name = $name;
												}
											}
										}
									}
								}
							}
							if ($slot == 'Talisman') {
								if ($armor_link_enhance == 'Defence') {
									if (!isset($t_talisman_def_pot)) { #This is the first time the item will be set.
										$t_talisman_def_pot = $potential;
										$t_talisman_def_index = $indexed;
										$t_talisman_def_stat = $def;
										$t_talisman_def_name = $name;
									} else { #Compare the item to a this current item.
										if ($t_talisman_def_pot < $potential) { #First we compare it with the item's potential.
											if ($t_talisman_def_stat < $def) { #Then, make sure the main stat is higher than the previous item
												#Now identify and compare all of the links for the item.
												if ($test >= 2) {
													#If you are missing X number of items required to make the armor link, don't consider the item.
													#Compare the boost of the item, and if the combined boost is greater than the previous set, replace the item.
													$t_talisman_def_pot = $potential;
													$t_talisman_def_index = $indexed;
													$t_talisman_def_stat = $def;
													$t_talisman_def_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Damage') {
									if (!isset($w_talisman_dmg_pot)) {
										$w_talisman_dmg_pot = $potential;
										$w_talisman_dmg_index = $indexed;
										$w_talisman_dmg_stat = $dmg;
										$w_talisman_dmg_name = $name;
									} else {
										if ($w_talisman_dmg_pot < $potential) {
											if ($w_talisman_dmg_stat < $dmg) {
												if ($test >= 2) {
													$w_talisman_dmg_pot = $potential;
													$w_talisman_dmg_index = $indexed;
													$w_talisman_dmg_stat = $dmg;
													$w_talisman_dmg_name = $name;
												}
											}
										}
									}
								}
								if ($armor_link_enhance == 'Magic') {
									if (!isset($m_talisman_magic_pot)) {
										$m_talisman_magic_pot = $potential;
										$m_talisman_magic_index = $indexed;
										$m_talisman_magic_stat = $magic;
										$m_talisman_magic_name = $name;
									} else {
										if ($m_talisman_magic_pot < $potential) {
											if ($m_talisman_magic_stat < $magic) {
												if ($test >= 2) {
													$m_talisman_magic_pot = $potential;
													$m_talisman_magic_index = $indexed;
													$m_talisman_magic_stat = $magic;
													$m_talisman_magic_name = $name;
												}
											}
										}
									}
								}
							}
							#Now lets pick some collection gear
							if (isset($t_collection_pick1_name)) {
								if (!in_array($t_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['def'][$t_def_1] = $t_collection_pick1_name;
										$t_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick1_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($t_collection_pick2_name)) {
								if (!in_array($t_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['def'][$t_def_1] = $t_collection_pick2_name;
										$t_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick2_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick1_name)) {
								if (!in_array($w_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['def'][$w_def_1] = $w_collection_pick1_name;
										$w_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick1_name;
											$whp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick2_name)) {
								if (!in_array($w_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['def'][$w_def_1] = $w_collection_pick2_name;
										$w_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick2_name;
											$whp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick1_name)) {
								if (!in_array($m_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['def'][$m_def_1] = $m_collection_pick1_name;
										$m_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick1_name;
											
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick2_name)) {
								if (!in_array($m_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['def']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['def'][$m_def_1] = $m_collection_pick2_name;
										$m_def_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick2_name;
											$mhp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							} #We don't have one for health, since there are no primary bonuses for health.
							################ Magic ###################
							if (isset($t_collection_pick1_name)) {
								if (!in_array($t_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['m'][$t_m_1] = $t_collection_pick1_name;
										$t_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick1_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($t_collection_pick2_name)) {
								if (!in_array($t_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['m'][$t_m_1] = $t_collection_pick2_name;
										$t_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick2_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick1_name)) {
								if (!in_array($w_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['m'][$w_m_1] = $w_collection_pick1_name;
										$w_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick1_name;
											$whp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick2_name)) {
								if (!in_array($w_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['m'][$w_m_1] = $w_collection_pick2_name;
										$w_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick2_name;
											$whp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick1_name)) {
								if (!in_array($m_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['m'][$m_m_1] = $m_collection_pick1_name;
										$m_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick1_name;
											
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick2_name)) {
								if (!in_array($m_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['m']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['m'][$m_m_1] = $m_collection_pick2_name;
										$m_m_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick2_name;
											$mhp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							}
							################ Attack ###################
							if (isset($t_collection_pick1_name)) {
								if (!in_array($t_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['dmg'][$t_dmg_1] = $t_collection_pick1_name;
										$t_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick1_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($t_collection_pick2_name)) {
								if (!in_array($t_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['t']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['t']['dmg'][$t_dmg_1] = $t_collection_pick2_name;
										$t_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['t']['hp']) < 5) {
											$col_array['t']['hp'][$thp1] = $t_collection_pick2_name;
											$thp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick1_name)) {
								if (!in_array($w_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['dmg'][$w_dmg_1] = $w_collection_pick1_name;
										$w_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick1_name;
											$whp1 +=1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($w_collection_pick2_name)) {
								if (!in_array($w_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['w']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['w']['dmg'][$w_dmg_1] = $w_collection_pick2_name;
										$w_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['w']['hp']) < 5) {
											$col_array['w']['hp'][$whp1] = $w_collection_pick2_name;
											$whp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick1_name)) {
								if (!in_array($m_collection_pick1_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['dmg'][$m_dmg_1] = $m_collection_pick1_name;
										$m_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick1_name;
											
										} #We should be able to use all equipped items.
									}
								}
							}
							if (isset($m_collection_pick2_name)) {
								if (!in_array($m_collection_pick2_name, $col_array)) { #Make sure we didn't already use this item.
									if (count($col_array['m']['dmg']) < 5) { #Don't save more than 5 values for one type.
										$col_array['m']['dmg'][$m_dmg_1] = $m_collection_pick2_name;
										$m_dmg_1 +=1;
									} else { #If you have more than 5, save it to HP
										if (count($col_array['m']['hp']) < 5) {
											$col_array['m']['hp'][$mhp1] = $m_collection_pick2_name;
											$mhp1 += 1;
										} #We should be able to use all equipped items.
									}
								}
							}
						}
					}
				}
				#echo '<pre>'; print_r($col_array); echo '</pre>';
				#exit;
				########## Equipped Gear ##########
				echo "<div class=\"t_title\">Tank Build</div>";
				echo "<div class=\"w_title\">Warrior Build</div>";
				echo "<div class=\"m_title\">Mage Build</div>";
				echo "<div class=\"t_helm_equip\">Tank Helm: $t_helm_def_name</div>";
				echo "<div class=\"w_helm_equip\">Warrior Helm: $w_helm_dmg_name</div>";
				echo "<div class=\"m_helm_equip\">Mage Helm: $m_helm_magic_name</div>";
				echo "<div class=\"t_chest_equip\">Tank Chest: $t_chest_def_name</div>";
				echo "<div class=\"w_chest_equip\">Warrior Chest: $w_chest_dmg_name</div>";
				echo "<div class=\"m_chest_equip\">Mage Chest: $m_chest_magic_name</div>";
				echo "<div class=\"t_glove_equip\">Tank Gloves: $t_gloves_def_name</div>";
				echo "<div class=\"w_glove_equip\">Warrior Gloves: $w_gloves_dmg_name</div>";
				echo "<div class=\"m_glove_equip\">Mage Gloves: $m_gloves_magic_name</div>";
				echo "<div class=\"t_feet_equip\">Tank Boots: $t_feet_def_name</div>";
				echo "<div class=\"w_feet_equip\">Warrior Boots: $w_feet_dmg_name</div>";
				echo "<div class=\"m_feet_equip\">Mage Boots: $m_feet_magic_name</div>";
				echo "<div class=\"t_ring_equip\">Tank Ring: $t_ring_def_name</div>";
				echo "<div class=\"w_ring_equip\">Warrior Ring: $w_ring_dmg_name</div>";
				echo "<div class=\"m_ring_equip\">Mage Ring: $m_ring_magic_name</div>";
				echo "<div class=\"t_neck_equip\">Tank Necklace: $t_amulet_def_name</div>";
				echo "<div class=\"w_neck_equip\">Warrior Necklace: $w_amulet_dmg_name</div>";
				echo "<div class=\"m_neck_equip\">Mage Necklace: $m_amulet_magic_name</div>";
				echo "<div class=\"t_talisman_equip\">Tank Talisman: $t_talisman_def_name</div>";
				echo "<div class=\"w_talisman_equip\">Warrior Talisman: $w_talisman_dmg_name</div>";
				echo "<div class=\"m_talisman_equip\">Mage Talisman: $m_talisman_magic_name</div>";

				########## Collection 1 ##########
				#Tank

				echo "<div class=\"t_col1_m1\">I: Magic Slot 1: " . $col_array['t']['m'][1] . "</div>";
				echo "<div class=\"t_col1_m2\">I: Magic Slot 2: " . $col_array['t']['m'][2] . "</div>";
				echo "<div class=\"t_col1_m3\">I: Magic Slot 3: " . $col_array['t']['m'][3] . "</div>";
				echo "<div class=\"t_col1_hp1\">I: HP Slot 1: " . $col_array['t']['hp'][1] . "</div>";
				echo "<div class=\"t_col1_hp2\">I: HP Slot 2: " . $col_array['t']['hp'][2] . "</div>";
				echo "<div class=\"t_col1_hp3\">I: HP Slot 3: " . $col_array['t']['hp'][3] . "</div>";
				echo "<div class=\"t_col1_def1\">I: Def Slot 1: " . $col_array['t']['def'][1] . "</div>";
				echo "<div class=\"t_col1_def2\">I: Def Slot 2: " . $col_array['t']['def'][2] . "</div>";
				echo "<div class=\"t_col1_dmg1\">I: Attack Slot 1: " . $col_array['t']['dmg'][1] . "</div>";
				echo "<div class=\"t_col1_dmg2\">I: Attack Slot 2: " . $col_array['t']['dmg'][2] . "</div>";
				#Warrior
				echo "<div class=\"w_col1_m1\">I: Magic Slot 1: " . $col_array['w']['m'][1] . "</div>";
				echo "<div class=\"w_col1_m2\">I: Magic Slot 2: " . $col_array['w']['m'][2] . "</div>";
				echo "<div class=\"w_col1_m3\">I: Magic Slot 3: " . $col_array['w']['m'][3] . "</div>";
				echo "<div class=\"w_col1_hp1\">I: HP Slot 1: " . $col_array['w']['hp'][1] . "</div>";
				echo "<div class=\"w_col1_hp2\">I: HP Slot 2: " . $col_array['w']['hp'][2] . "</div>";
				echo "<div class=\"w_col1_hp3\">I: HP Slot 3: " . $col_array['w']['hp'][3] . "</div>";
				echo "<div class=\"w_col1_def1\">I: Def Slot 1: " . $col_array['w']['def'][1] . "</div>";
				echo "<div class=\"w_col1_def2\">I: Def Slot 2: " . $col_array['w']['def'][2] . "</div>";
				echo "<div class=\"w_col1_dmg1\">I: Attack Slot 1: " . $col_array['w']['dmg'][1] . "</div>";
				echo "<div class=\"w_col1_dmg2\">I: Attack Slot 2: " . $col_array['w']['dmg'][2] . "</div>";
				#Mage
				echo "<div class=\"m_col1_m1\">I: Magic Slot 1: " . $col_array['m']['m'][1] . "</div>";
				echo "<div class=\"m_col1_m2\">I: Magic Slot 2: " . $col_array['m']['m'][2] . "</div>";
				echo "<div class=\"m_col1_m3\">I: Magic Slot 3: " . $col_array['m']['m'][3] . "</div>";
				echo "<div class=\"m_col1_hp1\">I: HP Slot 1: " . $col_array['m']['hp'][1] . "</div>";
				echo "<div class=\"m_col1_hp2\">I: HP Slot 2: " . $col_array['m']['hp'][2] . "</div>";
				echo "<div class=\"m_col1_hp3\">I: HP Slot 3: " . $col_array['m']['hp'][3] . "</div>";
				echo "<div class=\"m_col1_def1\">I: Def Slot 1: " . $col_array['m']['def'][1] . "</div>";
				echo "<div class=\"m_col1_def2\">I: Def Slot 2: " . $col_array['m']['def'][2] . "</div>";
				echo "<div class=\"m_col1_dmg1\">I: Attack Slot 1: " . $col_array['m']['dmg'][1] . "</div>";
				echo "<div class=\"m_col1_dmg2\">I: Attack Slot 2: " . $col_array['m']['dmg'][2] . "</div>";

				########## Collection 2 ##########
				#Tank
				echo "<div class=\"t_col2_m1\">II: Magic Slot 1: " . $col_array['t']['m'][4] . "</div>";
				echo "<div class=\"t_col2_m2\">II: Magic Slot 2: " . $col_array['t']['m'][5] . "</div>";
				echo "<div class=\"t_col2_hp1\">II: HP Slot 1: " . $col_array['t']['hp'][4] . "</div>";
				echo "<div class=\"t_col2_hp2\">II: HP Slot 2: " . $col_array['t']['hp'][5] . "</div>";
				echo "<div class=\"t_col2_def1\">II: Def Slot 1: " . $col_array['t']['def'][3] . "</div>";
				echo "<div class=\"t_col2_def2\">II: Def Slot 2: " . $col_array['t']['def'][4] . "</div>";
				echo "<div class=\"t_col2_def3\">II: Def Slot 3: " . $col_array['t']['def'][5] . "</div>";
				echo "<div class=\"t_col2_dmg1\">II: Attack Slot 1: " . $col_array['t']['dmg'][3] . "</div>";
				echo "<div class=\"t_col2_dmg2\">II: Attack Slot 2: " . $col_array['t']['dmg'][4] . "</div>";
				echo "<div class=\"t_col2_dmg3\">II: Attack Slot 3: " . $col_array['t']['dmg'][5] . "</div>";
				#Warrior
				echo "<div class=\"w_col2_m1\">II: Magic Slot 1: " . $col_array['w']['m'][4] . "</div>";
				echo "<div class=\"w_col2_m2\">II: Magic Slot 2: " . $col_array['w']['m'][5] . "</div>";
				echo "<div class=\"w_col2_hp1\">II: HP Slot 1: " . $col_array['w']['hp'][4] . "</div>";
				echo "<div class=\"w_col2_hp2\">II: HP Slot 2: " . $col_array['w']['hp'][5] . "</div>";
				echo "<div class=\"w_col2_def1\">II: Def Slot 1: " . $col_array['w']['def'][3] . "</div>";
				echo "<div class=\"w_col2_def2\">II: Def Slot 2: " . $col_array['w']['def'][4] . "</div>";
				echo "<div class=\"w_col2_def3\">II: Def Slot 3: " . $col_array['w']['def'][5] . "</div>";
				echo "<div class=\"w_col2_dmg1\">II: Attack Slot 1: " . $col_array['w']['dmg'][3] . "</div>";
				echo "<div class=\"w_col2_dmg2\">II: Attack Slot 2: " . $col_array['w']['dmg'][4] . "</div>";
				echo "<div class=\"w_col2_dmg3\">II: Attack Slot 3: " . $col_array['w']['dmg'][5] . "</div>";
				#Mage
				echo "<div class=\"m_col2_m1\">II: Magic Slot 1: " . $col_array['m']['m'][4] . "</div>";
				echo "<div class=\"m_col2_m2\">II: Magic Slot 2: " . $col_array['m']['m'][5] . "</div>";
				echo "<div class=\"m_col2_hp1\">II: HP Slot 1: " . $col_array['m']['hp'][4] . "</div>";
				echo "<div class=\"m_col2_hp2\">II: HP Slot 2: " . $col_array['m']['hp'][5] . "</div>";
				echo "<div class=\"m_col2_def1\">II: Def Slot 1: " . $col_array['m']['def'][3] . "</div>";
				echo "<div class=\"m_col2_def2\">II: Def Slot 2: " . $col_array['m']['def'][4] . "</div>";
				echo "<div class=\"m_col2_def3\">II: Def Slot 3: " . $col_array['m']['def'][5] . "</div>";
				echo "<div class=\"m_col2_dmg1\">II: Attack Slot 1: " . $col_array['m']['dmg'][3] . "</div>";
				echo "<div class=\"m_col2_dmg2\">II: Attack Slot 2: " . $col_array['m']['dmg'][4] . "</div>";
				echo "<div class=\"m_col2_dmg3\">II: Attack Slot 3: " . $col_array['m']['dmg'][5] . "</div>";
			}
			?>
			<div class="mark_placeholder"></div>
			<div class="mark_placeholder_txt"></div>


		</div>
		<div class="hero" class="container">
			<div class="descriptionText">
				<br><br><br><br>
				<h2>Work In Progress</h2>
				<p>The information displayed above is still in testing and incomplete.</p>
			</div>
		</div>
	</div>
</body>

</html>