<?php

namespace Estina\SmartFocus\Tests\Api\Rest;

use Estina\SmartFocus\Api\Rest\Member;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;

/**
 * Member Test
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class MemberTest extends PHPUnit_Framework_TestCase
{
    private $token = 'testtoken';
    private $xml = '<xml>testxml</xml>';
    private $email = 'test@example.com';

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
        $service->openConnection($server, $login, $password, $key);
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
     * tests insertMemberByEmailAddress
     */
    public function testInsertMemberByEmailAddress()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->insertMemberByEmailAddress($this->token, $this->email);
    }

    /**
     * tests insertMember
     */
    public function testInsertMember()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post');
        $service->insertMember($this->token, $this->xml);
    }

    /**
     * tests updateMember
     */
    public function testUpdateMember()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post');
        $service->updateMember($this->token, $this->xml);
    }

    /**
     * tests insertOrUpdateMember
     */
    public function testInsertOrUpdateMember()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post');
        $service->insertOrUpdateMember($this->token, $this->xml);
    }

    /**
     * tests updateMemberByEmailAddress
     */
    public function testUpdateMemberByEmailAddress()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->updateMemberByEmailAddress($this->token, $this->email, 'field', 'value');
    }

    /**
     * tests getMemberJobStatus
     */
    public function testGetMemberJobStatus()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->getMemberJobStatus($this->token, 'jobId');
    }

    /**
     * tests getMemberByEmail
     */
    public function testGetMemberByEmail()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->getMemberByEmail($this->token, $this->email);
    }

    /**
     * tests getMemberByCellphone
     */
    public function testGetMemberByCellphone()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->getMemberByCellphone($this->token, '123456789');
    }

    /**
     * tests getMemberById
     */
    public function testGetMemberById()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->getMemberById($this->token, '12345');
    }

    /**
     * tests getMembersByPage
     */
    public function testGetMembersByPage()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $service->getMembersByPage($this->token, '1');
    }

    /**
     * tests getMembersByCriteria
     */
    public function testGetMembersByCriteria()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post');
        $service->getMembersByCriteria($this->token, $this->xml);
    }

    /**
     * tests getMemberTableColumnNames
     */
    public function testGetMemberTableColumnNames()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->getMemberTableColumnNames($this->token);
    }

    /**
     * tests unsubscribeMemberByEmail
     */
    public function testUnsubscribeMemberByEmail()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->unsubscribeMemberByEmail($this->token, $this->email);
    }

    /**
     * tests unsubscribeMemberByCellphone
     */
    public function testUnsubscribeMemberByCellphone()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->unsubscribeMemberByCellphone($this->token, '123456789');
    }

    /**
     * tests unsubscribeMemberById
     */
    public function testUnsubscribeMemberById()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->unsubscribeMemberById($this->token, '12345');
    }

    /**
     * tests unsubscribeMemberByValue
     */
    public function testUnsubscribeMemberByValue()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('post');
        $response = $service->unsubscribeMemberByValue($this->token, $this->xml);
    }


    /**
     * tests resubscribeMemberByEmail
     */
    public function testResubscribeMemberByEmail()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->resubscribeMemberByEmail($this->token, $this->email);
    }

    /**
     * tests resubscribeMemberByCellphone
     */
    public function testResubscribeMemberByCellphone()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->resubscribeMemberByCellphone($this->token, '132456789');
    }

    /**
     * tests resubscribeMemberById
     */
    public function testResubscribeMemberById()
    {
        $service = $this->getService();
        $client = $this->getHiddenProperty($service, 'client');
        $client->expects($this->once())
               ->method('get');
        $response = $service->resubscribeMemberById($this->token, '13245');
    }


    /**
     * @return Member
     */
    private function getService($openConnection = true)
    {
        $client = $this->getMock('Estina\SmartFocus\Api\Http\CurlClient');
        $service = new Member($client);

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
