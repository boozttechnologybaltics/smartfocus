<?php

namespace Estina\SmartFocus\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;

use Estina\SmartFocus\Api\Rest\NotificationService;

/**
 * Individual Member Management REST API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NotificationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests send
     */
    public function testSend()
    {
        $email = 'test@example.com';
        $encrypt = 'The encrypt value';
        $notificationId = 'template ID';
        $random = 'The random value';
        $dyn = 'Dynamic personalization content';

        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->send($email, $encrypt, $notificationId, $random, $dyn);

    }

    /**
     * @return NotificationService
     */
    private function getService($openConnection = true)
    {
        $client = $this->getMock('Estina\SmartFocus\Api\Http\CurlClient');
        $service = new NotificationService($client);

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
