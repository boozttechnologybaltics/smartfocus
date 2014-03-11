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

### insertMemberByEmailAddress

```php
use Estina\SmartFocus\Api\Http\CurlClient;
use Estina\SmartFocus\Api\Rest\Member;
use Estina\SmartFocus\Api\Util\RestResponseParser;

$config = array(
    'server'    => 'server hostname',
    'login'     => 'your login name',
    'password'  => 'your password',
    'key'       => 'your API key',
);

$api = new Member(new CurlClient());
$parser = new ResponseParser();

$response = $api->openConnection($config['server'], $config['login'], $config['password'], $config['key']);
if ($response) {
    $token = $parser->getResult($response);
    if ($token) {
        $insertResponse = $api->insertMemberByEmailAddress($token, 'email@example.com');
        $closeResponse = $api->closeConnection($token);
    }
}
```

## Supported APIs and Methods

- [Member REST] (#member-rest)
- [Notification REST] (#notification-rest)

### Member REST

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


### Notification REST

- send($email, $encrypt, $notificationId, $random, $dyn, $senddate, $uidkey, $stype)

