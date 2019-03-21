<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" http-equiv="Cache-control" content="public" charset="utf-8">
	<title>Questland Orb Index</title>
	<meta name="description" content="Questland Index"/>
	<link href="css/style.css" rel="stylesheet">
	<script src="sorttable.js"></script>
</head>
<body>
	<div class="wrapper">
<!--_____________________________________Header____________________________________ -->
	<header>
		<div class="header-contents">
			<div class="intro">Questland Index</div>
		</div>
	</header>
<!--_____________________________________Navigation____________________________________ -->
    <header>
		<nav class="website-nav">
			<ul>
				<li><a class="home-link" href="index.php">Home</a></li>
				<li><a href="character.php">Manage Hero</a></li>
				<li><a href="weaponindex.php">Weapon Index</a></li>
				<li><a href="gearindex.php">Gear Index</a></li>
				<li><a href="runeindex.php">Orb Index</a></li>
			</ul>
		</nav> 
	</header>
<!--_____________________________________Page Content____________________________________ -->
<div class="center">
<?php
require __DIR__ . '/vendor/autoload.php'; #Used for local testing
use Aws\DynamoDb\SessionHandler;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\Credentials\CredentialProvider; #Used for Production 
$client = DynamoDbClient::factory([
	'region'  => 'us-east-1',
	'version' => 'latest',
	'credentials' => CredentialProvider::env() #Comment out this line to run with local creds.
]);

$params= [
	'TableName' => 'ql_untanglement',
	'Select' => 'ALL_ATTRIBUTES',
	'page-size' => 100
];

try {
	while (true){
		$result = $client->scan($params);
		echo '<table class="sortable">';
		echo "<tr>";
		echo '<th>Image ID</td>';
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
				echo '<td>',$value['prvw']['S'].'</td>';
				echo '<td>',$value['n']['S'].'</td>';
				echo '<td>',$value['q']['S'].'</td>';
				echo '<td>',$value['s']['S'].'</td>';
				if (!empty($value['stats']['M']['hp']['L'][0]['S'])){
					echo '<td>',$value['stats']['M']['hp']['L'][1]['S'].'</td>';
				}
				elseif (!empty($value['stats']['M']['dmg']['L'][0]['S'])){
					echo '<td>',$value['stats']['M']['dmg']['L'][1]['S'].'</td>';
				}
				elseif (!empty($value['stats']['M']['def']['L'][0]['S'])){
					echo '<td>',$value['stats']['M']['def']['L'][1]['S'].'</td>';
				}
				echo '<td>',$value['stats']['M']['hp']['L'][0]['S'].'</td>';
				echo '<td>',$value['stats']['M']['dmg']['L'][0]['S'].'</td>';
				echo '<td>',$value['stats']['M']['def']['L'][0]['S'].'</td>';
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
</body>
</html>
