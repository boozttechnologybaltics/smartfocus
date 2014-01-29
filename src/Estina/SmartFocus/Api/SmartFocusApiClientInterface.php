<?php

namespace Estina\SmartFocus\Api;

/**
 * Interface of SmartFocus API client
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface SmartFocusApiClientInterface
{
    /**
     * @param string $urlPrefix Common URL prefix for all calls
     */
    public function setUrlPrefix($urlPrefix);

    /**
     * Performs GET request
     *
     * @param string $url URL
     *
     * @return string|false
     */
    public function get($url);

    /**
     * Performs POST request
     *
     * @param string $url URL
     * @param string $xml XML request body
     *
     * @return string|false
     */
    public function post($url, $xml);
}
