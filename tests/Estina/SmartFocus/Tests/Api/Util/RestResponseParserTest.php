<?php

namespace Estina\SmartFocus\Tests;

use PHPUnit_Framework_TestCase;

use Estina\SmartFocus\Api\Util\RestResponseParser;

/**
 * RestResponseParser test
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class RestResponseParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests getResult
     */
    public function testGetResultThrowsExceptionOnEmptyResponse()
    {
        $service = $this->getService();
        $this->setExpectedException('InvalidArgumentException');
        $service->getResult('');
    }

    /**
     * Tests getResult
     */
    public function testGetResultThrowsExceptionOnInvalidResponse()
    {
        $service = $this->getService();
        $this->setExpectedException(
            'InvalidArgumentException',
            'Unknown error while parsing the response: <xml>invalid</xml>'
        );
        $service->getResult('<xml>invalid</xml>');
    }

    /**
     * Tests getResult
     */
    public function testGetResult()
    {
        $service = $this->getService();
        $result = $service->getResult('<xml><result>Hooray!</result></xml>');
        $this->assertEquals('Hooray!', $result);
    }

    /**
     * @return RestResponseParser
     */
    private function getService($openConnection = true)
    {
        return new RestResponseParser();
    }
}
