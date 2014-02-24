<?php

namespace Estina\SmartFocus\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;

use Estina\SmartFocus\Api\Http\DummyClient;
use Estina\SmartFocus\Api\Rest\DataIndividualUpdate;

/**
 * Individual Member Management REST API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class DataIndividualUpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests openConnection
     */
    public function testOpenConnection()
    {
        $server = 'localhost';
        $login = 'login';
        $password = 'password';
        $key = 'key';

        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->openConnection($server, $login, $password, $key);

    }

    /**
     * @return DataIndividualUpdate service
     */
    private function getService()
    {
        $client = $this->getMock('Estina\SmartFocus\Api\Http\CurlClient');
        return new DataIndividualUpdate($client);
    }


    /**
     * Return value protected/private property from object. Chaining could be
     * used to configure mocked objects:
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
