<?php

namespace Estina\SmartFocus\Api\Rest;

use Estina\SmartFocus\Api\SmartFocusApiClientInterface;

/**
 * Data Mass Update REST API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class DataMassUpdate
{
    /** @var Client */
    private $client;

    /**
     * Constructor
     *
     * @param SmartFocusApiClientInterface $client Instance of SmartFocus API client
     * @param string                       $server Web service host
     */
    public function __construct(SmartFocusApiClientInterface $client, $server)
    {
        $this->client = $client;
        $this->client->setUrlPrefix(sprintf('https://%s/apibatchmember/services/rest', $server));
    }

    /**
     * This method provides a session token when given valid credentials
     *
     * The token is valid for 60 minutes. To avoid problems with expired
     * tokens, it is recommended that you close your connection after an API
     * call and open a new connection for a new API call.
     *
     * @param string $login    The login provided for API access
     * @param string $password API password
     * @param string $key      The manager key copied from SmartFocus
     *
     * @return string|false
     */
    public function openConnection($login, $password, $key)
    {
        $response = $this->client->get("connect/open/$login/$password/$key");

        return $response;
    }

    /**
     * This method terminates the session token
     *
     * @param string $token token
     *
     * @return string|false
     */
    public function closeConnection($token)
    {
        $response = $this->client->get("connect/close/$token");

        return $response;
    }
}
