<?php

namespace Estina\Smartfocus\Api;

/**
 * Dummy Client for tests
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class SmartfocusApiDummyClient implements SmartfocusApiClientInterface
{
    /** @var string */
    private $urlPrefix;

    /**
     * @param string $urlPrefix Common URL prefix for all calls
     */
    public function setUrlPrefix($urlPrefix)
    {
        $this->urlPrefix = $urlPrefix;
    }

    /**
     * Performs GET request
     *
     * @param string $url URL
     *
     * @return string|false
     */
    public function get($url)
    {
        return $this->urlPrefix . '/' . $url;
    }

    /**
     * Performs POST request
     *
     * @param string $url URL
     * @param string $xml XML request body
     *
     * @return string|false
     */
    public function post($url, $xml)
    {
        return $this->urlPrefix . '/' . $url;
    }
}
