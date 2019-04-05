<?php
include 's3.php';
include 'dynamodb.php';
$bucket = 'elasticbeanstalk-us-east-1-331694059185';
    $value = '5317';
    $dbquery = [
        'TableName' => 'ql_dynamo',
        'KeyConditionExpression' => '#tag = :t',
        'ExpressionAttributeNames'=> [ '#tag' => 't' ],
        'ExpressionAttributeValues'=> array( ':t'  => array('N' => $value))
    ];
    $value = $client->query($dbquery);
    $objects = $s3Client->getIterator('ListObjects', array(
        'Bucket' => $bucket,
        'Prefix' => 'resources/storage/'
    ));
    foreach ($objects as $object) {
        
        if (strpos($object['Key'], $value['Items'][0]['prvw']["N"]) !== false) {
            $key = $object['Key'];
            break;
        }
    }
    
    echo "<img src=\"https://s3.amazonaws.com/$bucket/$key\" width=\"50\" height=\"50\"></img>";
    //$headers = $command->getResponse()->getHeaders();
    //echo $value['Items'][0]['i_hd']["N"];
?>