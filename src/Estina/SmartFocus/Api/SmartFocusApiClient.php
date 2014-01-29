<?php

namespace Estina\SmartFocus\Api;

/**
 * Simple object oriented cURL wrapper
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SmartFocusApiClient implements SmartFocusApiClientInterface
{
    /** @var string */
    private $urlPrefix;
    /** @var int */
    private $timeout;

    /**
     * Constructor
     *
     * @param int $timeout The maximum number of seconds to allow cURL functions to execute, default - 10
     */
    public function __construct($timeout = 10)
    {
        if (!function_exists('curl_init')) {
            throw new \Exception('cURL functions are not available, check if libcurl is installed.');
        }

        $this->timeout = $timeout;
    }

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
        $ch = $this->curlInit($this->getUrl($url));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
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
        $ch = $this->curlInit($this->getUrl($url));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Accept: application/xml'
        ));
        $response = curl_exec($ch);

        return $response;
    }

    /**
     * Initializes cURL session and sets common options
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException on invalid URL
     *
     * @return Resource
     */
    private function curlInit($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            throw new \InvalidArgumentException('Invalid URL: ' . $url);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        return $ch;
    }

    /**
     * Returns API URL
     *
     * @param string $url URL suffix
     *
     * @return string
     */
    private function getUrl($url)
    {
        return $this->urlPrefix . '/' . $url;
    }
}
