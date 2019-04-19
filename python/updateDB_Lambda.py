import json
from botocore.vendored import requests
import boto3
#Global Variables
jsonFile = "item_database"
Table = 'ql_dynamo'
TableCheck = True
def lambda_handler(event, context):
    # TODO implement
    identify_update()
    
def identify_update():
    r=requests.get('http://gs-bhs-wrk-02.api-ql.com/client/checkstaticdata/?lang=en&graphics_quality=hd_android')
    json_data = json.loads(r.text)
    checked_file = json_data['data']['static_data']['crc_details']['item_templates']
    s3Client = boto3.client("s3")
    response = s3Client.get_object(
        Bucket='elasticbeanstalk-us-east-1-331694059185',
        Key='resources/item_templates')
    file_content = response['Body'].read()
    currentDB = json.loads(file_content)
    if (checked_file == currentDB['hash']):
        download_update
        
        #First Setup, load the data file into a json dump to be parsed.
        with open(jsonFile) as myfile:
            myFile = json.load(myfile)
        client = boto3.client('dynamodb')
        try:
            client.describe_table(
                TableName= Table
            )
            TableCheck = True
        except:
            TableCheck = False

        table = validate_Table()

        for index in myFile:
            newIndex = convert_Item(index)
            upload_DynamoDB(table, newIndex['t'], newIndex)

def download_update():
    print("Downloading Update")
    #Write the items like 

def validate_Table():
    dynamodb = boto3.resource('dynamodb')
    #Validate the table exists
    if TableCheck is False:
        print (Table + " does not exist. Creating...")
        # Create the DynamoDB table.
        table = dynamodb.create_table(
            TableName=Table,
            KeySchema=[
                {
                    'AttributeName': 't',
                    'KeyType': 'HASH'
                },
                {
                    'AttributeName': 'n',
                    'KeyType': 'RANGE'
                }
            ],
            AttributeDefinitions=[
                {
                    'AttributeName': 't',
                    'AttributeType': 'N'
                },
                {
                    'AttributeName': 'n',
                    'AttributeType': 'S'
                },
            ],
            ProvisionedThroughput={
                'ReadCapacityUnits': 5,
                'WriteCapacityUnits': 5
            }
        )
        table.meta.client.get_waiter('table_exists').wait(TableName=Table)
        if table.item_count == 0:
            print("Completed table creation.")
    else:
        print(Table + " exists.")
        table = dynamodb.Table(Table)
    return table
    
#Now that we have the file, convert it into a dynamodb file format.
def upload_DynamoDB(table, itemIndex,newItem):
    # Use the t key for an index and validate if the item already exists.
        print ("Validating index item: " + str(itemIndex))
        try:
            table.get_item(
                Key={
                    't': itemIndex
                }
            )
            itemCheck = True
        except:
            itemCheck = False
        
        if itemCheck == False:
            if itemIndex != 5437:
                print("Adding " + newItem['n'])
                table.put_item(Item=newItem)
        else:
            print("Updating " + newItem['n'])
            table.delete_item(
                Key={
                    't': itemIndex,
                    'n': newItem['n']
                }
            )
            table.put_item(Item=newItem)

def convert_Item(index):
    #Remove blank string values from d property.
    if index['d'] == '':
        index['d'] = 'Null'
    if 'banner' in index:
        if index['banner']['c'] == '':
            index['banner']['c'] = 'Null'
    if 'loot_chances' in index:
        if type(index['loot_chances']) is not list:
            if 'wearable' in index['loot_chances']:
                for key, value in index['loot_chances']['wearable'].items():
                    index['loot_chances']['wearable'][key] = str(value)
            if 'orb' in index['loot_chances']:
                for key, value in index['loot_chances']['orb'].items():
                    index['loot_chances']['orb'][key] = str(value)
            if 'part' in index['loot_chances']:
                for key, value in index['loot_chances']['part'].items():
                    index['loot_chances']['part'][key] = str(value)
    return index

    