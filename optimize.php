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
			#include 'dynamodb.php';
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
				#include 's3.php';
				#include 'dynamodb.php';
				#These zeroed values will be used to define the collection array.
				$t_def_1 = 0;
				$w_def_1 = 0;
				$def_1 = 0;
				$t_dmg_1 = 0;
				$w_dmg_1 = 0;
				$dmg_1 = 0;
				$t_1 = 0;
				$w_1 = 0;
				$col1 = 0;
				$thp1=0;
				$whp1=0;
				$mhp1=0;

				$set_itemList[] = array();
				$itemList[] = array();
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
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$wearable_sets/wearable_sets/");
				$setList = json_decode(curl_exec($ch),true);
				curl_close($ch);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$static_passive_skills/wearable_sets/");
				$weaponPassive = json_decode(curl_exec($ch),true);
				curl_close($ch);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, "http://gs-bhs-wrk-01.api-ql.com/staticdata/key/en/android/$set_itemlist/item_templates/");
				$itemList = json_decode(curl_exec($ch));
				curl_close($ch);
				$col_array = array();
				$ch = curl_init();
				$curl_headers = array(
					"token: $token",
				);
				$myitemList = array();
				$myitemList = $get_hero['data']['items_list'];
				$player_id = $SESSION['player_id'];
				curl_setopt($ch, CURLOPT_URL, 'http://149.56.27.225/user/getprofile/?hero_id=' . $player_id);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$player_data = json_decode(curl_exec($ch), true);
				curl_close($ch);
				function findKey($array, $keySearch)
				{
					foreach ($array as $key => $item) {
						if ($key == $keySearch) {
							return $keySearch;
						} elseif (is_array($item) && findKey($item, $keySearch)) {
							return $keySearch;
						}
					}
					return false;
				}
		#This whole thing can be read in two parts
		#Part 1: Index all of the currently owned gear and find out if user has at least two pieces to get the link
		#		We want to put a weight on the items, where if a link in one item is the same as a link from another pick, we add 1 to the picked item weight.
		#		Additionally, sum the potential the filtered item and two best links so we can compare it to the next item.
		#		Ex. Summed Head Item 2 is 10 potential lower, but one of the linked items has a weight that is 2 higher than Summed Head Item 1, Head 2 is more valuable.
		#	Once the index is complete, we should have three arrays. 
				# 1. The origianl $itemArray which contains all items available.
				# 2. The filtered $collection_picks containing all useable items (There should be at least one item in each main slot)
				# 3. The weighted $link_list
		#	We only need 14 armor links so pick the top items from the weighted list until the sum of the weight equals 14.
		#	Now make one more array, which contains all items picked for the main slots and links.

				foreach ($itemList as $key => $item) { #Lets make an array from the itemList called $itemArray
					$tag=NULL;
					$quality=NULL;
					$slot=NULL;
					$tag=$itemList[$key]->t;
					$slot=ucfirst($itemList[$key]->s);
					$quality = ucfirst($itemList[$key]->q);
					if ($quality == 'Legendary' || $quality == 'Artifact1' || $quality == 'Artifact2' || $quality == 'Artifact2' || $quality == 'Artifact3' || $quality == 'Artifact4') {
						if  ($slot == 'Amulet' || $slot == 'Talisman' || $slot == 'Head'|| $slot == 'Chest'|| $slot == 'Gloves'|| $slot == 'Feet'|| $slot == 'Ring' || $slot == 'Exactshard'){
							$itemArray[$tag] = [
								"name" => $itemList[$key]->n,
								"slot" => $slot,
								"quality" => $quality,
								"potential" => $itemList[$key]->stats->def[1] + $itemList[$key]->stats->hp[1] + $itemList[$key]->stats->dmg[1] + $itemList[$key]->stats->magic[1],
								"link1" => $itemList[$key]->links[0]->i[0][0],
								"link2" => $itemList[$key]->links[0]->i[1][0],
								"link3" => $itemList[$key]->links[0]->i[2][0],
							];
						};
					};
				};

				$heroList = $get_hero['data']['items_list'];
				foreach ($heroList as $key => $item) {#Generate an array of all the hero gear
					$tag=NULL;
					$quality=NULL;
					$slot=NULL;
					$link1=NULL;
					$link2=NULL;
					$link3=NULL;
					$link1_array=NULL;
					$link2_array=NULL;
					$link3_array=NULL;
					$tag=findKey($itemArray, $item['a'][1]);
					$slot=ucfirst($itemArray[$tag]['slot']);
					if (isset($item["wear"]) == FALSE) { //Item is in shards, so we need to convert it to an item if there are enough shards.
						if ($slot == 'Exactshard'){ #Make sure we don't select any orbs or special items.
							$parts = $item['a'][5];
						};
						if ($parts >= 30) { #There need to be at least 30 parts for consideration.
							#If there are enough parts, convert the part tag into a gear tag.
							$shardName=$itemArray[$tag]['name'];
							foreach ($itemList as $mybasekey => $mybaseitem) {
								#Search the itemList for the part name, and make sure the part is only legendary and the tags don't match so we don't select the shard from that list.
								if ($mybaseitem->n == $shardName && $mybaseitem->q == 'legendary' && $mybaseitem->t != $tag) {
									$tag=$mybaseitem->t;
									$slot=ucfirst($mybaseitem->s);
									break 1; #Once it gets a match, stop looking.
								};
							};
						};
					};
					$quality = ucfirst($itemArray[$tag]['quality']);
					if ($quality == 'Legendary' || $quality == 'Artifact1' || $quality == 'Artifact2' || $quality == 'Artifact2' || $quality == 'Artifact3' || $quality == 'Artifact4') {
						if  ($slot == 'Amulet' || $slot == 'Talisman' || $slot == 'Head'|| $slot == 'Chest'|| $slot == 'Gloves'|| $slot == 'Feet'|| $slot == 'Ring'){
							#This ensures we only check the appropriate gear, so lets get some details about the links.
							$link1=$itemArray[$tag]['link1'];
							$link2=$itemArray[$tag]['link2'];
							$link3=$itemArray[$tag]['link3'];
							foreach ($itemArray as $linkKey => $linkItem) {
								if ($link1 == $linkKey){
									$link1_array[$link1]=[
										"link_name" => $linkItem['name'],
										"link_potential" => $linkItem['potential'],
										"link_slot" => $linkItem['slot'],
										"link_l1" => $linkItem['link1'],
										"link_l2" => $linkItem['link2'],
										"link_l3" => $linkItem['link3'],
									];
								};
								if ($link2 == $linkKey){
									$link2_array[$link2]=[
										"link_name" => $linkItem['name'],
										"link_potential" => $linkItem['potential'],
										"link_slot" => $linkItem['slot'],
										"link_l1" => $linkItem['link1'],
										"link_l2" => $linkItem['link2'],
										"link_l3" => $linkItem['link3'],
									];
								};
								if ($link3 == $linkKey){
									$link3_array[$link3]=[
										"link_name" => $linkItem['name'],
										"link_potential" => $linkItem['potential'],
										"link_slot" => $linkItem['slot'],
										"link_l1" => $linkItem['link1'],
										"link_l2" => $linkItem['link2'],
										"link_l3" => $linkItem['link3'],
									];
								};
								
							};
							$heroArray[$tag] = [
								"name" => $itemArray[$tag]['name'],
								"slot" => $slot,
								"quality" => $quality,
								"potential" => $itemArray[$tag]['potential'],
								"link1" => $link1_array,
								"link2" => $link2_array,
								"link3" => $link3_array,
							];
						};
					};
				};

				#Now that our items are filted and the array is setup, create a weighted list for each piece of linked armor.
				foreach($heroArray as $filterKey => $filterItem){
					#if ($filterItem['quality'] == 'Legendary'){ #I'm filtering anything but legendary to help with gear potential skew
						$weight=NULL;
						$potential=NULL;
						$quality=NULL;
						$name=NULL;
						$slot=NULL;
						$name=$filterItem['name'];
						$slot=$filterItem['slot'];
						$potential=$filterItem['potential'];
						if (isset($weightedList)){ #Make sure we have an initial entry before attempting to update values.
							#Add the three linked items plus the original item to the list, updating weights if they are already in the weighted array.
							if ($name == findKey($weightedList, $name)){
								$weight=$weightedList[$name]['weight'] + 1;
								$weightedList[$name] = [
									'weight' => $weight,
									'potential' => $potential,
									'slot' => $slot,	
								];
							} else {
								$weight = 1;
								$weightedList[$name] = [
									'weight' => $weight,
									'potential' => $potential,
									'slot' => $slot,	
								];
							};
							if (array_values($filterItem['link1'])[0]['link_name'] == findKey($weightedList, array_values($filterItem['link1'])[0]['link_name'])){
								$weight=$weightedList[array_values($filterItem['link1'])[0]['link_name']]['weight'] + 1;
								$weightedList[array_values($filterItem['link1'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link1'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link1'])[0]['link_slot'],	
								];
							} else {
								$weight = 1;
								$weightedList[array_values($filterItem['link1'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link1'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link1'])[0]['link_slot'],	
								];
							};
							if (array_values($filterItem['link2'])[0]['link_name'] == findKey($weightedList, array_values($filterItem['link2'])[0]['link_name'])){
								$weight=$weightedList[array_values($filterItem['link2'])[0]['link_name']]['weight'] + 1;
								$weightedList[array_values($filterItem['link2'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link2'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link2'])[0]['link_slot'],	
								];
							} else {
								$weight = 1;
								$weightedList[array_values($filterItem['link2'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link2'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link2'])[0]['link_slot'],	
								];
							};
							if (array_values($filterItem['link3'])[0]['link_name'] == findKey($weightedList, array_values($filterItem['link3'])[0]['link_name'])){
								$weight=$weightedList[array_values($filterItem['link3'])[0]['link_name']]['weight'] + 1;
								$weightedList[array_values($filterItem['link3'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link3'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link3'])[0]['link_slot'],	
								];
							} else {
								$weight = 1;
								$weightedList[array_values($filterItem['link3'])[0]['link_name']] = [
									'weight' => $weight,
									'potential' => array_values($filterItem['link3'])[0]['link_potential'],	
									'slot' => array_values($filterItem['link3'])[0]['link_slot'],	
								];
							};
						} else {
							$weight = 1;
							$weightedList[$name] = [
								'weight' => $weight,
								'potential' => $potential,
								'slot' => $slot,	
							];
							$weightedList[array_values($filterItem['link1'])[0]['link_name']] = [
								'weight' => $weight,
								'potential' => array_values($filterItem['link3'])[0]['link_potential'],	
								'slot' => array_values($filterItem['link1'])[0]['link_slot'],	
							];
							$weightedList[array_values($filterItem['link2'])[0]['link_name']] = [
								'weight' => $weight,
								'potential' => array_values($filterItem['link3'])[0]['link_potential'],	
								'slot' => array_values($filterItem['link2'])[0]['link_slot'],	
							];
							$weightedList[array_values($filterItem['link3'])[0]['link_name']] = [
								'weight' => $weight,
								'potential' => array_values($filterItem['link3'])[0]['link_potential'],	
								'slot' => array_values($filterItem['link3'])[0]['link_slot'],	
							];
						};
					#};
				};
				#Now we have our equippable items and the weighted list.
				#In this sequence:
					#Find the highest potential items for each slot and update the equip field on that weighted item.
					#If B has more Potential and the same or more links than A, replace A with Item B for that slot and update the equip field on that item.
				foreach ($weightedList as $equipKey => $equipItem){
					if (isSet($equipArray)){
						if (findKey($equipArray,$equipItem['slot']) == $equipItem['slot']){ #First, check if the slot is already used.
							#The slot is used, so we need to compare the one currently set.
							if ($equipItem['potential'] > $equipArray[$equipItem['slot']]['potential'] && $equipItem['weight'] >= 5){ 
								$equipArray[$equipItem['slot']]=[
									'potential' => $equipItem['potential'],
									'weight' => $equipItem['weight'],
									'name' => $equipKey,
								];
							}
						} else {  #If the slot isn't used, assume we should add it to the list.
							$equipArray[$equipItem['slot']]=[
								'potential' => $equipItem['potential'],
								'weight' => $equipItem['weight'],
								'name' => $equipKey,
							];
						}
					} else{
						$equipArray[$equipItem['slot']]=[ #This should be the slot for the item
							'potential' => $equipItem['potential'], #The potential of the item.
							'weight' => $equipItem['weight'],
							'name' => $equipKey,
						];
					}
				};
				#This is the last step, where the program has picked the best items for each main slot. We need to make one more array to keep track of the main items.
				#We have to ensure that each slot only gets 2 links for each position.
				#Now we want it to use the weighted list to find the best items to make the base links:
					#If the item makes a link with a main equipped item, and there 
					#If B has more potential and the same or more links than another items, replace one item
				#echo '<pre>'; print_r($equipArray); echo '</pre>';

				########## Equipped Gear ##########
				$helm=$equipArray['Head']['name'];
				$chest=$equipArray['Chest']['name'];
				$gloves=$equipArray['Gloves']['name'];
				$feet=$equipArray['Feet']['name'];
				$ring=$equipArray['Ring']['name'];
				$amulet=$equipArray['Amulet']['name'];
				$talisman=$equipArray['Talisman']['name'];
				echo "<div class=\"t_title\">Main Equipment</div>";
				echo "<div class=\"t_helm_equip\">Helm: $helm</div>";
				echo "<div class=\"t_chest_equip\">Chest: $chest</div>";
				echo "<div class=\"t_glove_equip\">Gloves: $gloves</div>";
				echo "<div class=\"t_feet_equip\">Boots: $feet</div>";
				echo "<div class=\"t_ring_equip\">Ring: $ring</div>";
				echo "<div class=\"t_neck_equip\">Necklace: $amulet</div>";
				echo "<div class=\"t_talisman_equip\">Talisman: $talisman</div>";

			};
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