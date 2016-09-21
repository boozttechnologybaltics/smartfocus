<?php

namespace Estina\SmartFocus\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Estina\SmartFocus\Api\Rest\Notification;
use SimpleXMLElement;

/**
 * Notification REST Service test.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @group notification
 * @group unit
 */
class NotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests send.
     */
    public function testSend()
    {
        $email = 'test@example.com';
        $encrypt = 'The encrypt value';
        $notificationId = 'template ID';
        $random = 'The random value';
        $dyn = 'field1:value1|field2:value2';

        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->send($email, $encrypt, $notificationId, $random, $dyn);
    }

    /**
     * @return Notification
     */
    private function getService($openConnection = true)
    {
        $client = $this->getMock('Estina\SmartFocus\Api\Http\CurlClient');
        $service = new Notification($client);

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

    /**
     * testPost Test that post executes as expected.
     */
    public function testPost()
    {
        // Prepare / Mock
        $service = $this->getService();
        $xmlObjectMock = new SimpleXMLElement('<request></request>');

        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post')
               ->with($this->isType('string'), $this->isType('string'))
               ->will($this->returnValue('success'));

        // Execute
        $result = $service->post($xmlObjectMock);

        // Assert Result
        $this->assertEquals('success', $result);
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithoutDynAndContent()
    {
        $service = $this->getService();
        $params = [
            'encrypt' => 'asdf',
            'random' => 'asdjhfk',
            'email' => 'real@email.com',
            'send_date' => '10.05.2016',
            'synchro_type' => 'xml',
            'uid_key' => '271162'
        ];

        $result = $service->buildTransactionalRequestObject($params);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertFalse(property_exists($result->sendrequest, 'dyn'));
        $this->assertFalse(property_exists($result->sendrequest, 'content'));
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithDyn()
    {
        $service = $this->getService();
        $params = [
            'encrypt' => 'asdf',
            'random' => 'asdjhfk',
            'email' => 'real@email.com',
            'send_date' => '10.05.2016',
            'synchro_type' => 'xml',
            'uid_key' => '271162'
        ];
        $dyn = [
            'name' => 'Abdul',
            'email' => 'abdul@easyfundraising.org.uk'
        ];

        $result = $service->buildTransactionalRequestObject($params, $dyn);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->dyn);
        $this->assertEquals($result->sendrequest->dyn->entry[0]->key, 'name');
        $this->assertEquals($result->sendrequest->dyn->entry[0]->value, 'Abdul');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->key, 'email');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->value, 'abdul@easyfundraising.org.uk');

        $this->assertFalse(property_exists($result->sendrequest, 'content'));
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithDynAndContentAndTracking()
    {
        $service = $this->getService();
        $params = [
            'encrypt' => 'asdf',
            'random' => 'asdjhfk',
            'email' => 'real@email.com',
            'send_date' => '10.05.2016',
            'synchro_type' => 'xml',
            'uid_key' => '271162'
        ];
        $dyn = [
            'name' => 'Abdul',
            'email' => 'abdul@easyfundraising.org.uk'
        ];
        $content = [
            'click <a href="https://track.this/up?enabled=true">here</a>',
            "click <a href='https://track.this/up?enabled=true&index=2#stuff'>here 2</a>",
            'image <img src="https://dont.track/this?up=false">'
        ];

        $result = $service->buildTransactionalRequestObject($params, $dyn, $content, true);
        $result2 = $service->buildTransactionalRequestObject($params, $dyn, $content);

        // Assert the default is not true for tracking.
        $this->assertNotEquals($result, $result2);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->dyn);
        $this->assertEquals($result->sendrequest->dyn->entry[0]->key, 'name');
        $this->assertEquals($result->sendrequest->dyn->entry[0]->value, 'Abdul');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->key, 'email');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->value, 'abdul@easyfundraising.org.uk');

        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->content);
        $this->assertTrue(property_exists($result->sendrequest, 'content'));
        // Emv tags added.
        $this->assertEquals($result->sendrequest->content->entry[0]->value, 'click <a href="[EMV URL]https://track.this/up?enabled=true[EMV /URL]">here</a>');
        // Emv tags added.
        $this->assertEquals($result->sendrequest->content->entry[1]->value, "click <a href='[EMV URL]https://track.this/up?enabled=true&index=2#stuff[EMV /URL]'>here 2</a>");
        // Unchanged.
        $this->assertEquals($result->sendrequest->content->entry[2]->value, 'image <img src="https://dont.track/this?up=false">');
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithDynAndContentAndWithoutTracking()
    {
        $service = $this->getService();
        $params = [
            'encrypt' => 'asdf',
            'random' => 'asdjhfk',
            'email' => 'real@email.com',
            'send_date' => '10.05.2016',
            'synchro_type' => 'xml',
            'uid_key' => '271162'
        ];
        $dyn = [
            'name' => 'Abdul',
            'email' => 'abdul@easyfundraising.org.uk'
        ];
        $content = [
            'click <a href="https://track.this/up?enabled=true">here</a>',
            "click <a href='https://track.this/up?enabled=true&index=2#stuff'>here 2</a>",
            'image <img src="https://dont.track/this?up=false">'
        ];

        $result = $service->buildTransactionalRequestObject($params, $dyn, $content, false);
        $result2 = $service->buildTransactionalRequestObject($params, $dyn, $content);

        // Assert the default is false for tracking.
        $this->assertEquals($result, $result2);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->dyn);
        $this->assertEquals($result->sendrequest->dyn->entry[0]->key, 'name');
        $this->assertEquals($result->sendrequest->dyn->entry[0]->value, 'Abdul');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->key, 'email');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->value, 'abdul@easyfundraising.org.uk');

        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->content);
        $this->assertTrue(property_exists($result->sendrequest, 'content'));
        // Emv tags added.
        $this->assertEquals($result->sendrequest->content->entry[0]->value, 'click <a href="https://track.this/up?enabled=true">here</a>');
        // Emv tags added.
        $this->assertEquals($result->sendrequest->content->entry[1]->value, "click <a href='https://track.this/up?enabled=true&index=2#stuff'>here 2</a>");
        // Unchanged.
        $this->assertEquals($result->sendrequest->content->entry[2]->value, 'image <img src="https://dont.track/this?up=false">');
    }
}
