<?php

namespace Estina\SmartFocus\Api\Rest;

use Estina\SmartFocus\Api\Http\ClientInterface;

/**
 * Abstract class for all Rest services
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractRestService
{
    /** @var ClientInterface HTTP client */
    protected $client;
    /** @var string */
    private $urlPrefix;

    /**
     * Constructor
     *
     * @param ClientInterface $client Instance of SmartFocus API HTTP client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $prefix URL prefix common to all API calls
     */
    protected function setUrlPrefix($prefix)
    {
        $this->urlPrefix = $prefix;
    }

    /**
     * Returns API URL
     *
     * @param string $url URL suffix
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getUrl($url)
    {
        $result = $this->urlPrefix . '/' . $url;

        if (false === filter_var($result, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf('Invalid URL generated: %s', $result));
        }

        return $result;
    }
}
