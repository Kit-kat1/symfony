<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/19/15
 * Time: 4:23 PM
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Form\UsersType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Users;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class ProductRepositoryFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $client;
    private $id;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testCreateUserSuccess()
    {
        $this->client->request(
            'POST',
            '/admin/user/save',
            array('username' => 'user'),
            array('email' => 'john@doe.com'),
            array('firstName' => 'User'),
            array('lastName' => 'Surname'),
            array('phoneNumber' => '0955845738'),
            array('roles' => array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER')),
            array('enabled' => '1'),
            array('password' => 'qwerty')
        );

        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'user1'));
        $this->id = $user->getId();
        $this->assertEquals(1, count($user));
    }


    public function testDeleteUserSuccess()
    {
        $this->client->request(
            'DELETE',
            '/admin/user/delete' . $this->id
        );

        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('id' => $this->id));
        $this->assertEquals(0, count($user));
    }

//    public function testDeleteUserFailed()
//    {
//        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'user5'));
//        $this->em->remove($user);
//        $this->em->flush();
//
//        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'user5'));
//        $this->assertEquals(0, count($user));
//    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
