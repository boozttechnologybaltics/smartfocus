<?php

namespace Estina\SmartFocus\Api\Http;

/**
 * Interface of SmartFocus API HTTP client
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ClientInterface
{
    /**
     * Performs GET request
     *
     * @param string $url URL
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function get($url);

    /**
     * Performs POST request
     *
     * @param string $url URL
     * @param string $xml XML request body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function post($url, $xml);

    /**
     * Performs PUT request
     *
     * @param string $url    URL
     * @param array  $header Header
     * @param string $body   Body
     *
     * @return mixed - XML string or FALSE in failure
     */
    public function put($url, $header, $body);
}
