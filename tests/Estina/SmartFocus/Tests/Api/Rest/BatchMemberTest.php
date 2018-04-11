<?php

namespace Estina\SmartFocus\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;

use Estina\SmartFocus\Api\Rest\BatchMember;

/**
 * Batch Member Test
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BatchMemberTest extends PHPUnit_Framework_TestCase
{
    private $token = 'testtoken';

    /**
     * Tests openConnection
     */
    public function testOpenConnection()
    {
        $server = 'localhost';
        $login = 'login';
        $password = 'password';
        $key = 'key';

        $service = $this->getService(false);
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->openConnection($server, $login, $password, $key);

    }

    /*
     * Tests closeConnection without opening it first
     */
    public function testCloseConnectionWithoutOpenConnection()
    {
        $this->setExpectedException('InvalidArgumentException');
        $service = $this->getService(false);
        $service->closeConnection($this->token);
    }

    /**
     * Tests closeConnection
     */
    public function testCloseConnection()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->closeConnection($this->token);
    }

    /**
     * @return BatchMember
     */
    private function getService($openConnection = true)
    {
        $client = $this->getMock('Estina\SmartFocus\Api\Http\CurlClient');
        $service = new BatchMember($client);

        if ($openConnection) {
            $server = 'localhost';
            $login = 'login';
            $password = 'password';
            $key = 'key';

            $service->openConnection($server, $login, $password, $key);
        }

        return $service;
    }

    /**
     * Return value protected/private property from object.
     *
     * @param object $object Target object
     * @param string $name   Name of hidden property
     *
     * @return object
     */
    private function getHiddenProperty($object, $name)
    {
        $refl = new ReflectionProperty(get_class($object), $name);
        $refl->setAccessible(true);

        return $refl->getValue($object);
    }
}
