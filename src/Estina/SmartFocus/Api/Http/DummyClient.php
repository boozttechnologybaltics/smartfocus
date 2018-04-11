<?php

namespace Estina\SmartFocus\Api\Http;

/**
 * Dummy Client for tests
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class DummyClient implements ClientInterface
{
    /**
     * Performs GET request
     *
     * @param string $url URL
     *
     * @return string
     */
    public function get($url)
    {
        return $url;
    }

    /**
     * Performs POST request
     *
     * @param string $url URL
     * @param string $xml XML request body
     *
     * @return string
     */
    public function post($url, $xml)
    {
        return $xml;
    }
}
