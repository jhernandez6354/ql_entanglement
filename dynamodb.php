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
	#'credentials' => CredentialProvider::env() #Comment out this line to run locally. (You'll need aws creds)
]);

$params= [
	'TableName' => 'ql_dynamo',
	'Select' => 'ALL_ATTRIBUTES',
	'page-size' => 100
];
?>