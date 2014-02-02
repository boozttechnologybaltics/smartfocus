<?php

namespace Estina\SmartFocus\Api\Rest;

/**
 * Parser of Smartfocus REST API responses
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResponseParser
{
    /**
     * Constructor
     *
     * @throws \Exception if libxml is not installed
     */
    public function __construct()
    {
        if (!function_exists('simplexml_load_string')) {
            throw new \Exception('simplexml_load_string function is not available, check if libxml is installed.');
        }
    }

    /**
     * Returns token from the response string
     *
     * @param string $response Response
     *
     * @throws \Exception - when it's not possible to parse the response
     *
     * @return string
     */
    public function getToken($response)
    {
        $xml = simplexml_load_string($response);
        if (!$xml instanceof \SimpleXMLElement) {
            throw new \Exception(sprintf('Cannot parse the response: %s', $response));
        }

        if (!isset($xml->result)) {
            $message = $xml->description ?: sprintf('Unknown error while parsing the response: %s.', $response);
            throw new \Exception($message);
        }

        return (string) $xml->result;
    }
}
