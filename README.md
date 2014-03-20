# PHP SmartFocus API

<img src="https://raw.github.com/estina/smartfocus/master/sf_logo.png" align="right" width="245px" />

This is a PHP library for SmartFocus API.

This library provides access to SmartFocus (previously known as EmailVision) [Campaign Commander's APIs]
(http://developer.smartfocus.com/io-docs). It was designed with flexibility in mind, with fully decoupled components, so it would be easy for developer
to inject and use his own components where appropriate.


## Requirements

- PHP 5.x+
- curl
- libxml


## Install

Add the following to composer.json:

    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:Estina/smartfocus.git"
        }
    ]

followed by:

    "require": {
        "estina/smartfocus": "dev-master"
    }

update as you normally do:

    php composer.phar update


## Examples

This library can be used in two ways:

- [Low level] (#low-level-api) - when you need flexibility and access "under the hood"
- High level - when you need a quick and simple access (not implemented yet)

### Low Level API

Interacting with all low level API functionality can be trimmed down to these basic steps:

- open the connection and extract security token from the XML response
- call the API method(s) using token and other required parameters
- close the connection

Please, check the [examples] (#low-level-api-examples) below.

HTTP transport to the actual REST interface is implemented in
Api\Http\CurlClient class. It's possible to use different class object as long
as it implements a very simple Api\Http\ClientInterface.

#### Supported APIs and Methods

- [Member REST] (#member-rest) - individual subscription management
- [Notification REST] (#notification-rest) - notification (email sending) service

##### Member REST

- openConnection($server, $login, $password, $key)
- closeConnection($token)
- insertMemberByEmailAddress($token, $email)
- insertMember($token, $xml)
- updateMember($token, $xml)
- insertOrUpdateMember($token, $xml)
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


##### Notification REST

- send($email, $encrypt, $notificationId, $random, $dyn, $senddate, $uidkey, $stype)

### Low Level API Examples

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
    $parser = new ResponseParser();
    $token = $parser->getResult($xmlResponse);

    if ($token) {
        // call the API method
        $insertResponse = $api->insertMemberByEmailAddress($token, 'email@example.com');
        // close the connection
        $api->closeConnection($token);
    }
}
```

## More information

More documentation is available in the "doc" folder and in the source code.



