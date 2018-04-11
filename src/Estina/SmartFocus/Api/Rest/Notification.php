<?php

namespace Estina\SmartFocus\Api\Rest;

use Estina\SmartFocus\Api\Http\ClientInterface;
use SimpleXMLElement;

/**
 * Transactional Messaging Trigger API.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Notification extends AbstractRestService
{
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);

        $this->setUrlPrefix('https://api.notificationmessaging.com');
    }

    /**
     * @param string $email          The email address to which you wish to send the transactional message
     * @param string $encrypt        The encrypt value provided in the interface
     * @param string $notificationId The ID of the template - currently ignored - random & uidkey determine template?
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
        if (empty($uidkey)) {
            throw new \InvalidArgumentException('uidkey must not be blank');
        }

        $params = array(
            'random' => $random,
            'encrypt' => $encrypt,
            'email' => $email,
            'senddate' => $senddate,
            'uidkey' => $uidkey,
            'stype' => $stype
        );

        $response = $this->client->get(
            $this->getUrl("NMSREST?" . http_build_query($params) . "&dyn=" . $dyn)
        );

        return $response;
    }

    /**
     * Send batch of email transactions at once.
     *
     * Also supports sending of dynamic content.
     *
     * @param SimpleXMLElement $xmlObject The xml object to post.
     *
     * @return string|false XML response or FALSE on failure
     */
    public function post(SimpleXMLElement $xmlObject)
    {
        $response = $this->client->post(
            $this->getUrl("NMSXML"),
            $xmlObject->asXML()
        );

        return $response;
    }

    /**
     * Builds up a transactional xml request object for batch processing.
     *
     * @param string $recipientEmail The recipient email.
     * @param string $encryptId The encrypt id for the template.
     * @param string $randomId The random id for the template.
     * @param array|null $dyn The dynamic content.
     * @param array|null $content The content block links.
     * @param bool $enableTracking Set EMV URL in the urls found?
     * @param array $additionalParams Any additional params to send off.
     *
     * @return SimpleXMLElement
     */
    public function buildTransactionalRequestObject(
        $recipientEmail,
        $encryptId,
        $randomId,
        array $dyn = null,
        array $content = null,
        $enableTracking = false,
        array $additionalParams = null
    ) {
        $xmlObject = new SimpleXMLElement('<MultiSendRequest></MultiSendRequest>');

        $sendRequest = $xmlObject->addChild('sendrequest');
        $sendRequest->addChild('email');
        $xmlObject->sendrequest->email = $recipientEmail;
        $sendRequest->addChild('encrypt');
        $xmlObject->sendrequest->encrypt = $encryptId;
        $sendRequest->addChild('random');
        $xmlObject->sendrequest->random = $randomId;

        if (is_array($additionalParams)) {
            foreach ($additionalParams as $key => $value) {
                $sendRequest->addChild($key);
                $sendRequest->$key = $value;
            }
        }

        if (is_array($dyn)) {
            $this->setDynInXMLObject($xmlObject->sendrequest, $dyn);
        }

        if (is_array($content)) {
            $this->setContentInXMLObject($xmlObject->sendrequest, $content, $enableTracking);
        }

        return $xmlObject;
    }

    /**
     * Sets the dyn tags in the xmlObject.
     *
     * @param datatype $xmlObject The xml object to set the dyn in.
     * @param array $dyn The dyn tags.
     *
     * @return void
     */
    private function setDynInXMLObject($xmlObject, array $dyn)
    {
        $xmlObject->addChild('dyn');

        // Add dyn tags to the request object.
        foreach ($dyn as $key => $value) {
            $entry = $xmlObject->dyn->addChild('entry');
            $entry->key = $key;
            $entry->value = $value;
        }
    }

    /**
     * Sets the content in the xmlObject.
     *
     * @param datatype $xmlObject The xml object to set the content in.
     * @param array $content The dynamic content blocks to set.
     * @param bool $enableTracking Set EMV URL in the urls found?
     *
     * @return void
     */
    private function setContentInXMLObject($xmlObject, array $content, $enableTracking)
    {
        $xmlObject->addChild('content');

        // Html markup url expression.
        $hrefExpr = '%href=([\'"])?((https?|ftp):\/\/[^\'" >]+)%i';

        // Add Dynamic content blocks to the request object.
        foreach ($content as $k => $v) {
            if ($enableTracking) {
                // Setup url tracking using emv tags.
                $v = preg_replace($hrefExpr, 'href=$1[EMV URL]$2[EMV /URL]', $v);
                // Replace the pound symbol for its htmlentity.
                $v = str_replace('£', '&pound;', $v);
            }

            $entry = $xmlObject->content->addChild('entry');
            $entry->key = $k;
            $entry->value = $v;
        }
    }
}
