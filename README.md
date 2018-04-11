# PHP SmartFocus API

<img src="https://raw.github.com/estina/smartfocus/master/sf_logo.png" align="right" width="245px" />

This is a PHP library for SmartFocus API.

This library provides access to SmartFocus (previously known as EmailVision) [Campaign Commander's APIs](https://help-developer.smartfocus.com/#Home.htm%3FTocPath%3D_____1). It was designed with flexibility in mind, with fully decoupled components, so it would be easy for developer
to inject and use his own components where appropriate.

## Requirements

- PHP 5.3+
- curl
- libxml

## Install

    composer require estina/smartfocus

### Usage

Interacting with API can be trimmed down to these basic steps:

- open the connection and extract security token from the XML response
- call the API method(s) using token and other required parameters
- close the connection

Please, check the [examples] (#api-examples) below.

HTTP transport to the actual REST interface is implemented in
Api\Http\CurlClient class. It's possible to use different class object as long
as it implements a very simple Api\Http\ClientInterface.

#### Supported APIs and Methods

- [Member REST](#member-rest) - individual subscription management
- [Batch Member REST](#batch-member-rest) - batch subscription management
- [Notification REST](#notification-rest) - notification (email sending) service

##### Member REST

- openConnection($server, $login, $password, $key)
- closeConnection($token)
- [insertMemberByEmailAddress($token, $email)] (#memberinsertmemberbyemailaddresstoken-email)
- insertMember($token, $xml)
- updateMember($token, $xml)
- [insertOrUpdateMember($token, $xml)] (#memberinsertorupdatemembertoken-xml)
- updateMemberByEmailAddress($token, $email, $field, $value)
- getMemberJobStatus($token, $jobId)
- getMemberByEmail($token, $email)
- getMemberByCellphone($token, $cellphone)
- getMemberById($token, $memberId)
- getMembersByPage($token, $page)
- getMembersByCriteria($token, $xml)
- getMemberTableColumnNames($token)
- unsubscribeMemberByEmail($token, $email)
- unsubscribeMemberByCellphone($token, $cellphone)
- unsubscribeMemberById($token, $memberId)
- unsubscribeMemberByValue($token, $xml)
- resubscribeMemberByEmail($token, $email)
- resubscribeMemberByCellphone($token, $cellphone)
- resubscribeMemberById($token, $memberId)

##### Batch Member REST

- openConnection($server, $login, $password, $key)
- closeConnection($token)
- buildInsertXml($filepath, $dateformat = 'yyyy-MM-dd', $dedup = true)
- insert($token, $xml)
- buildUpdateXml($filepath, $dateformat = 'yyyy-MM-dd')
- update($token, $xml)
- getLastUpload($token)
- getUploadStatus($token, $uploadId)
- getUploads($token, $page, $pageSize = 1000, $sort = null, $status = null, $source = null)
- getLogFile($token, $uploadId)
- getBadFile($token, $uploadId)


##### Notification REST

- [send($email, $encrypt, $notificationId, $random, $dyn, $senddate, $uidkey, $stype)] (#notificationsendemail-encrypt-notificationid-random-dyn-senddate-uidkey-stype)
- buildTransactionalRequestObject($recipientEmail, $encryptId, $randomId, $dyn, $content, $enableTracking, $additionalParams)
- post(SimpleXMLElement $xmlRequestObject)

### API Examples

#### Member::insertMemberByEmailAddress($token, $email)

```php
// cURL client for communication with API
use Estina\SmartFocus\Api\Http\CurlClient;
// Member REST API class
use Estina\SmartFocus\Api\Rest\Member;
// helper for XML parsing (optional)
use Estina\SmartFocus\Api\Util\RestResponseParser;

// initialize object, injecting the cURL client
$api = new Member(new CurlClient());

// open the connection, XML is returned, containing token or error description
$xmlResponse = $api->openConnection(
    'server hostname',
    'your login name',
    'your password',
    'your API key'
);

if ($xmlResponse) {
    // extract token from XML
    $parser = new RestResponseParser();
    $token = $parser->getResult($xmlResponse);

    if ($token) {

        // call the API method and fetch the response
        $insertResponse = $api->insertMemberByEmailAddress(
            $token,
            'email@example.com'
        );

        // close the connection
        $api->closeConnection($token);
    }
}
```

#### Member::insertOrUpdateMember($token, $xml)

```php
// cURL client for communication with API
use Estina\SmartFocus\Api\Http\CurlClient;
// Member REST API class
use Estina\SmartFocus\Api\Rest\Member;
// helper for XML parsing (optional)
use Estina\SmartFocus\Api\Util\RestResponseParser;

// initialize object, injecting the cURL client
$api = new Member(new CurlClient());

// open the connection, XML is returned, containing token or error description
$xmlResponse = $api->openConnection(
    'server hostname',
    'your login name',
    'your password',
    'your API key'
);

if ($xmlResponse) {
    // extract token from XML
    $parser = new RestResponseParser();
    $token = $parser->getResult($xmlResponse);

    if ($token) {

        /*
         * Let's build request's XML body with data
         *
         * <memberUID>:
         *      Multiple sets of criteria can be combined to identify the member, e.g.
         *      EMAIL:johnsmith@smartfocus.com|LASTNAME:Smith
         *
         * <dynContent>:
         *      envelope containing the list of fields to be updated and their values
         *
         * <entry>:
         *      The entry envelope containing the field to update and its value.
         *
         * <key>:
         *      The field that will be updated.
         *
         * <value>:
         *      The value with which to update the field.
         */
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>
             <synchroMember>
                <memberUID>EMAIL:%s</memberUID>
                <dynContent>
                    <entry>
                        <key>FIRSTNAME</key>
                        <value>%s</value>
                    </entry>
                    <entry>
                        <key>LASTNAME</key>
                        <value>%s</value>
                    </entry>
                </dynContent>
            </synchroMember>';

        // inject values into XML
        $xml = sprintf($xml, 'email@example.com', 'John', 'Smith');

        // call the API method and fetch the response
        $insertResponse = $api->insertOrUpdateMember($token, $xml);
        // close the connection
        $api->closeConnection($token);
    }
}
```

#### Notification::send($email, $encrypt, $notificationId, $random, $dyn, $senddate, $uidkey, $stype)

```php
// cURL client for communication with API
use Estina\SmartFocus\Api\Http\CurlClient;
// Member REST API class
use Estina\SmartFocus\Api\Rest\Notification;

// initialize object, injecting the cURL client
$api = new Notification(new CurlClient());

$response = $api->send(
    'email@example.com',             // Recipient
    'abcdefg',                       // Encrypt value provided in the interface
    '132456',                        // ID of the Template
    '123456789',                     // Random value provided for the template
    'firstname:John|lastname:Smith', // Dynamic parameters as a string
    'YYYY-MM-DD HH:MM:SS',           // optional, The time you wish to send
    'email',                         // Now *REQUIRED* - the key you wish to update, normally email
    'NOTHING'                        // optional, The type of synchronization
);
```
### Notification::post(SimpleXMLElement $xmlRequestObject)
```php
// cURL client for communication with API
use Estina\SmartFocus\Api\Http\CurlClient;
// Member REST API class
use Estina\SmartFocus\Api\Rest\Notification;

// initialize object, injecting the cURL client
$api = new Notification(new CurlClient());

$recipientEmail = 'email@example.com';
$encryptId = 'abcdefg';
$randomId = '132456';

$additionalParams = [
    'senddate'      => 'YYYY-MM-DDTHH:MM:SS', // 'T' between date & time
    'uidkey'        => 'email',
    'synchrotype'   => 'NOTHING'
];

// Optional: Dynamic parameters as an array
$dyn = [
    'firstname' => 'John',
    'lastname' => 'Smith'
];

$content = [
    'click <a href="http://somewhere.com">here</a> please',
    'good stuff is available <a href="http://goodstuff.com">here</a>'
];

// Tracking enabled for the links passed in the content blocks.
$enableTracking = true;

// Build request object.
$xmlRequestObject = $api->buildTransactionalRequestObject(
    $recipientEmail,
    $encryptId,
    $randomId,
    $dyn,
    $content,
    $enableTracking,
    $additionalParams
);

// Make the request.
$response = $api->post($xmlRequestObject);
```

## Troubleshooting

If you get the response with the following status codes:

- UPDATE_MEMBER_FAILED
- ISTUPD_MEMBER_FAILED

Check the following:

- XML input body is formatted correctly
- There are no spaces, newlines or other invalid symbols inside the <memberUID>
  tag, it should look like:

```xml
<memberUID>EMAIL:email@example.com</memberUID>
```

## Unit test

    composer test

## More information

SmartFocus documentation is available in the "doc" folder. Also, more detailed
descriptions of all functions and their parameters are available in the source
code.
