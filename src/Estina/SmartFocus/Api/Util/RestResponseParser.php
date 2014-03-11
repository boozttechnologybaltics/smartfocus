<?php

namespace Estina\SmartFocus\Api\Util;

/**
 * Parser of Smartfocus REST API responses
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class RestResponseParser
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
     * Returns value of the node "result" from the response XML string
     *
     * @param string $response Response
     *
     * @throws \InvalidArgumentException - when it's not possible to parse the response
     *
     * @return string
     */
    public function getResult($response)
    {
        $xml = simplexml_load_string($response);
        if (!$xml instanceof \SimpleXMLElement) {
            throw new \InvalidArgumentException(sprintf('Cannot parse the response: %s', $response));
        }

        if (!isset($xml->result)) {
            $message = $xml->description ?: sprintf('Unknown error while parsing the response: %s.', $response);
            throw new \InvalidArgumentException($message);
        }

        return (string) $xml->result;
    }
}
