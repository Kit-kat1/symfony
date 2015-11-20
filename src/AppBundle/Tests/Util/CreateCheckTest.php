<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/20/15
 * Time: 5:53 PM
 */

namespace CreateCheckTest;

use AppBundle\Util\CreateCheck;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Util\DeleteCheck;

class CreateCheckTest extends KernelTestCase
{
    /**
     * @var object
     */
    private $container;
    private $id;

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer()
            ->get('service_container')
        ;
    }

    public function testDeleteSuccess()
    {
        $check = new CreateCheck($this->container);

        $body = 'name=Ping&type=http&host=hostname.ua';
        $response = $check->create($body);

        $this->id = $response['check']['id'];

        $this->assertEquals('Ping', $response['check']['name']);
    }

    //Send to Pingdom invalid body. Response should return error array with statuscode 400 (Bad request)
    public function testCreateFailed()
    {
        $check = new CreateCheck($this->container);
        $body = 'name=pingdom&type=http&host=http://hostname.ua';

        $response = $check->create($body);

        $this->assertEquals($response['error']['statuscode'], 400);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $deleteCheck = new DeleteCheck($this->container);
        $deleteCheck->delete($this->id);
    }
}
