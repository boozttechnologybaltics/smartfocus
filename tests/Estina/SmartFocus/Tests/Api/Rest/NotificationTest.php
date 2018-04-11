<?php

namespace Estina\SmartFocus\Tests;

use Estina\SmartFocus\Api\Rest\Notification;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
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
    public function testSendSuccess()
    {
        $email = 'test@example.com';
        $encrypt = 'The encrypt value';
        $notificationId = 'template ID';
        $random = 'The random value';
        $dyn = 'field1:value1|field2:value2';

        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->never())
            ->method('get');
        $client->expects($this->once())
            ->method('post');

        $service->send($email, $encrypt, $notificationId, $random, $dyn, '2018-12-12 00:00:00', 'someuidkey');

        $this->addToAssertionCount(1); // We've implicitly asserted no exceptions hit
    }

    public function testSendWithUidkeyMissing()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'uidkey must not be blank');

        $email = 'test@example.com';
        $encrypt = 'The encrypt value';
        $notificationId = 'template ID';
        $random = 'The random value';
        $dyn = 'field1:value1|field2:value2';

        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->never())
            ->method('post');

        $service->send($email, $encrypt, $notificationId, $random, $dyn);
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
        $email = 'real@email.com';
        $encryptId = 'asdf';
        $randomId = 'asdjhfk';
        $uidkey = 'myUidKey';

        $result = $service->buildTransactionalRequestObject(
            $email,
            $encryptId,
            $randomId,
            null,
            null,
            false,
            array('uidkey' => $uidkey)
        );

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertFalse(property_exists($result->sendrequest, 'dyn'));
        $this->assertFalse(property_exists($result->sendrequest, 'content'));
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithDynAndAdditionalParams()
    {
        $service = $this->getService();

        $email = 'real@email.com';
        $encryptId = 'asdf';
        $randomId = 'asdjhfk';

        $dyn = [
            'name' => 'Abdul',
            'email' => 'abdul@easyfundraising.org.uk'
        ];

        $additionalParams = array('senddate' => '10.05.2016', 'stuff' => 'xyz', 'uidkey' => 'someUidKey');

        $result = $service->buildTransactionalRequestObject(
            $email,
            $encryptId,
            $randomId,
            $dyn,
            null,
            false,
            $additionalParams
        );

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertInstanceOf(SimpleXMLElement::class, $result->sendrequest->dyn);
        $this->assertEquals($result->sendrequest->dyn->entry[0]->key, 'name');
        $this->assertEquals($result->sendrequest->dyn->entry[0]->value, 'Abdul');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->key, 'email');
        $this->assertEquals($result->sendrequest->dyn->entry[1]->value, 'abdul@easyfundraising.org.uk');

        $this->assertFalse(property_exists($result->sendrequest, 'content'));
        $this->assertTrue(property_exists($result->sendrequest, 'senddate'));
        $this->assertEquals($result->sendrequest->senddate, '10.05.2016');
        $this->assertEquals($result->sendrequest->stuff, 'xyz');
    }

    /**
     * Test that buildTransactionalRequestObject works as expected.
     */
    public function testBuildTransactionalRequestObjectWithDynAndContentAndTracking()
    {
        $service = $this->getService();

        $email = 'real@email.com';
        $encryptId = 'asdf';
        $randomId = 'asdjhfk';

        $params = [
            'send_date' => '10.05.2016',
            'synchro_type' => 'xml',
            'uidkey' => '271162'
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

        $result = $service->buildTransactionalRequestObject($email, $encryptId, $randomId, $dyn, $content, true, $params);

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

        $email = 'real@email.com';
        $encryptId = 'asdf';
        $randomId = 'asdjhfk';

        $dyn = [
            'name' => 'Abdul',
            'email' => 'abdul@easyfundraising.org.uk'
        ];
        $content = [
            'click <a href="https://track.this/up?enabled=true">here</a>',
            "click <a href='https://track.this/up?enabled=true&index=2#stuff'>here 2</a>",
            'image <img src="https://dont.track/this?up=false">'
        ];
        $params = array(
            'uidkey' => 'theUidKey',
        );

        $result = $service->buildTransactionalRequestObject($email, $encryptId, $randomId, $dyn, $content, false, $params);
        $result2 = $service->buildTransactionalRequestObject($email, $encryptId, $randomId, $dyn, $content, null, $params);

        // Assert the default is false for tracking. TODO probably remove this? it doesn't really test much now.
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
}
