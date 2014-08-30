<?php

namespace Estina\SmartFocus\Api\Rest;

/**
 * Transactional Messaging Trigger API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Notification extends AbstractRestService
{
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
        $senddate = '2008-12-12 00:00:00',
        $uidkey = '',
        $stype = 'NOTHING'
    ) {

        $this->setUrlPrefix('http://api.notificationmessaging.com');

        $params = array(
            'random' => $random,
            'encrypt' => $encrypt,
            'email' => $email,
            'senddate' => $senddate,
            'uidkey' => $uidkey,
            'stype' => $stype
        );
        
        if (is_array($dyn)) {
            $dyn = http_build_query($dyn, '', '|');
            $dyn = str_replace('=', ':', $dyn);
        }

        $response = $this->client->get(
            $this->getUrl("NMSREST?" . http_build_query($params) . "&dyn=" . $dyn)
        );

        return $response;
    }
}
