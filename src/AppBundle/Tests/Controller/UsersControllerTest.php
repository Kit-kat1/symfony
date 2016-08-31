<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/19/15
 * Time: 4:23 PM
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class ProductRepositoryFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $client;

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

    //Equals should be true. User created in db.
    public function testCreateUserSuccess()
    {
        $this->client->request(
            'POST',
            '/admin/user/save',
            array(
                'users' =>
                    array('username' => 'john', 'email' => 'john@doe.com', 'firstName' => 'User',
                        'lastName' => 'Surname', 'phoneNumber' => '0955845738', 'roles' =>
                        array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'), 'enabled' => '1', 'password' => 'qwerty'))
        );

        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'john'));
        $this->assertEquals(1, count($user));
    }

    //Equals true. Because there is no user with username - user4. (User not created)
    public function testCreateUserFailed()
    {
        $this->client->request(
            'POST',
            '/admin/user/save',
            array(
                'users' =>
                    array('username' => 'user4', 'email' => 'john@mail.com', 'firstName' => 'User',
                        'lastName' => 'Surname', 'phoneNumber' => '093427567236', 'roles' =>
                        array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'), 'enabled' => '1', 'password' => 'qwerty'))
        );


        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'user4'));
        $this->assertEquals(0, count($user));
    }

    public function createUser()
    {
        $user = new Users();
        $user->setUpdated();
        $user->setUsername('user2');
        $user->setFirstName('John');
        $user->setLastName('Smith');
        $user->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user->setPassword($encoder->encodePassword('qwerty', $user->getSalt()));
        $user->setEmail('jonny@mail.ru');
        $user->setPhoneNumber('1');
        $user->setPhoneNumber('0947810623');

        $this->em->persist($user);
        $this->em->flush();

        return $user->getId();
    }

    //User deleted. Fetching will return 0 rows. Equals (0 to 0) - true
    public function testDeleteUserSuccess()
    {
        $this->createUser();
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'user2'));

        $this->client->request(
            'DELETE',
            '/admin/user/delete/' . $user->getId()
        );

        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('id' => $user->getId()));
        $this->assertEquals(0, count($user));
    }

    //Delete request should return 404. Because such user does not find.
    public function testDeleteUserFailed()
    {
        $this->client->request(
            'DELETE',
            '/admin/user/delete/' . 1000000
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    //Equals should be true. User updated in db.
    public function testEditUserSuccess()
    {
        $id = $this->createUser();
        $this->client->request(
            'PUT',
            '/admin/user/save/' . $id,
            array(
                'users' =>
                    array('username' => 'name', 'email' => 'joseph@mail.com', 'firstName' => 'User',
                        'lastName' => 'Surname', 'phoneNumber' => '09342753336', 'roles' =>
                        array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'), 'enabled' => '1', 'password' => 'qwerty'))
        );

        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('email' => 'joseph@mail.com'));
        $this->assertEquals(1, count($user));
    }

    //Delete request should return 404. Because such user does not find.
    public function testEditUserFailed()
    {
        $id = $this->createUser();
        $this->client->request(
            'PUT',
            '/admin/user/save/' . 100000,
            array(
                'users' =>
                    array('username' => 'name', 'email' => 'joe@mail.com', 'firstName' => 'User',
                        'lastName' => 'Surname', 'phoneNumber' => '09342753336', 'roles' =>
                        array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'), 'enabled' => '1', 'password' => 'qwerty'))
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $users = $this->em
            ->createQuery("SELECT u FROM AppBundle:Users u WHERE u.username NOT IN ('admin', 'user')")
            ->getResult();

        foreach ($users as $user) {
            $this->em->remove($user);
        }
        $this->em->flush();
    }
}
