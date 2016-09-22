<?php

namespace Estina\SmartFocus\Api\Http;


/**
 * Simple object oriented cURL wrapper.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class CurlClient implements ClientInterface
{
    /** @var int */
    private $timeout;

    /**
     * Constructor.
     *
     * @param int $timeout The maximum number of seconds to allow cURL functions to execute, default - 10
     */
    public function __construct($timeout = 10)
    {
        if (!function_exists('curl_init')) {
            throw new \Exception('cURL functions are not available, check if libcurl is installed.');
        }

        $this->setTimeout($timeout);
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Performs GET request.
     *
     * @param string $url URL
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function get($url)
    {
        $ch = $this->curlInit($url);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Performs POST request.
     *
     * @param string $url URL
     * @param string $xml XML request body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function post($url, $xml)
    {
        $ch = $this->curlInit($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Accept: application/xml'
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Performs PUT request.
     *
     * @param string $url    URL
     * @param array  $header Header
     * @param string $body   Body
     *
     * @return mixed - XML string or FALSE in failure
     */
    public function put($url, $header, $body)
    {
        $ch = $this->curlInit($url);

        $header[] = 'Accept: application/xml';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Initializes cURL session and sets common options.
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
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeout());
        curl_setopt($ch, CURLOPT_HEADER, 0);

        return $ch;
    }
}
