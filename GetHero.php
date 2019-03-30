<?php
    include 'dynamodb.php';

    $aResult = array();

    if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

    if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

    if( !isset($aResult['error']) ) {

        switch($_POST['functionname']) {
            case 'heroGear':
               if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
                   $aResult['error'] = 'Error in arguments!';
               }
							 else { //Meat and Potatoes
								try {
										$result = $client->scan($params);
										$gear =($_POST['arguments']);
										foreach ($result['Items'] as $value) {
											if($value['t']['N'] == $gear[1]['a'][1]){
												$aResult['result']['feet'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[1]['wear'][1]
													,"boost" => $gear[1]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[2]['a'][1]){
												$aResult['result']['chest'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[2]['wear'][1]
													,"boost" => $gear[2]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[3]['a'][1]){
												$aResult['result']['helm'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[3]['wear'][1]
													,"boost" => $gear[3]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[4]['a'][1]){
												$aResult['result']['neck'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[4]['wear'][1]
													,"boost" => $gear[4]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[5]['a'][1]){
												$aResult['result']['hand'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[5]['wear'][1]
													,"boost" => $gear[5]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[6]['a'][1]){
												$aResult['result']['ring'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[6]['wear'][1]
													,"boost" => $gear[6]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[7]['a'][1]){
												$aResult['result']['idol'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[7]['wear'][1]
													,"boost" => $gear[7]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[8]['a'][1]){
												$aResult['result']['mh'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[8]['wear'][1]
													,"boost" => $gear[8]['wear'][2]
												);
											}
											if($value['t']['N'] == $gear[0]['a'][1]){
												$aResult['result']['oh'] = array(
													"name" => $value['n']['S']
													,"image" => $value['i_hd']['N']
													,"upgrade" => $gear[0]['wear'][1]
													,"boost" => $gear[0]['wear'][2]
												);
											}
									};
										
									if (isset($result['LastEvaluatedKey'])) {
										$params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
									} else {
										break;
									}
								} catch (DynamoDbException $e) {
										echo "Unable to scan:\n";
										echo $e->getMessage() . "\n";
								}
              }
              break;

            default:
              $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
              break;
        }

    }

    echo json_encode($aResult);
?>