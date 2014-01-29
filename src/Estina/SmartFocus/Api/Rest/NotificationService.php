<?php

namespace Estina\SmartFocus\Api\Rest;

use Estina\SmartFocus\Api\SmartFocusApiClientInterface;

/**
 * Transactional Messaging Trigger API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NotificationService
{
    /** @var Client */
    private $client;

    /**
     * Constructor
     *
     * @param SmartFocusApiClientInterface $client Instance of SmartFocus API client
     */
    public function __construct(SmartFocusApiClientInterface $client)
    {
        $this->client = $client;
        $this->client->setUrlPrefix('http://api.notificationmessaging.com');
    }

    /**
     * @param string $email          The email address to which you wish to send the transactional message
     * @param string $encrypt        The encrypt value provided in the interface
     * @param string $notificationId The ID of the Template
     * @param string $random         The random value provided for the template
     * @param string $dyn            Dynamic personalization content, format: "syncKey:value|field:value|field:value"
     * @param string $senddate       The time you wish to send the Transactional Message, (time in the past = send now)
     * @param string $uidkey         The key you wish to update, normally its email
     * @param string $stype          The type of synchronization that should be carried out
     *
     * @return string|false XML response or FALSE on failure
     */
    public function send(
        $email,
        $encrypt,
        $notificationId,
        $random,
        $dyn,
        $senddate = '2008-12-12T00:00:0',
        $uidkey = '',
        $stype = 'NOTHING'
    ) {
        $params = array(
            'random' => $random,
            'encrypt' => $encrypt,
            'email' => $email,
            'senddate' => $senddate,
            'uidkey' => $uidkey,
            'stype' => $stype,
            'dyn' => $dyn,
        );

        $url = "NMSREST?" . http_build_query($data);
        $response = $this->client->get($url);

        return $response;
    }
}
