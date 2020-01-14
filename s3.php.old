<?php
require __DIR__ . '/vendor/autoload.php'; #Used for local testing
use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
// Create a client object
$s3Client = S3Client::factory(array(
    'region'  => 'us-east-1',
    'version' => 'latest',
    'Bucket' => 'elasticbeanstalk-us-east-1-331694059185',
    'Prefix' => 'resources/storage/',
	'credentials' => CredentialProvider::env() #Comment out this line to run locally. (You'll need aws creds)
));
$s3Client->registerStreamWrapper();
?>