<?php

namespace Estina\Smartfocus\Api;

use PHPUnit_Framework_TestCase;

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
        $login = 'login';
        $password = 'password';
        $key = 'key';

        $service = $this->getService();
        $response = $service->openConnection($login, $password, $key);
        var_dump($response);
    }

    /**
     * @return DataIndividualUpdate service
     */
    private function getService()
    {
        return new Rest\DataIndividualUpdate(new SmartfocusApiDummyClient(), 'localhost');
    }


}
