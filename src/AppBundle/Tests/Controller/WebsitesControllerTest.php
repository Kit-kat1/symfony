<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/20/15
 * Time: 11:49 AM
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Form\WebsitesType;
use AppBundle\Util\DeleteCheck;
use AppBundle\Util\PrepareDataToManipulateCheck;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Websites;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class WebsitesControllerTest extends WebTestCase
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

    public function testCreateWebsiteSuccess()
    {
        $this->client->request(
            'POST',
            '/profile/website/save',
            array(
                'websites' =>
                    array('name' => 'site', 'url' => 'mysite.com', 'status' => 'up'))
        );

        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'site'));
        $this->assertEquals(1, count($website));
    }

    public function testCreateWebsiteFailed()
    {
        $this->client->request(
            'POST',
            '/profile/website/save',
            array(
                'websites' =>
                    array('name' => 'Newsite', 'url' => 'site.com', 'status' => 'down'))
        );



        $user = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'Newsite'));
        $this->assertEquals(0, count($user));
    }

    public function createWebsite()
    {
        $website = new Websites();
        $website->setUpdated();
        $website->setName('Some site');
        $website->setUrl('awesome.com');
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(array('username' => 'admin'));
        $website->setOwner($user);
        $website->setStatus('up');

        $this->em->persist($website);
        $this->em->flush();

        return ['id_w' => $website->getId(), 'id_u' => $user->getId()];
    }

    public function testDeleteWebsiteSuccess()
    {
        $this->createWebsite();
        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'Some site'));

        $this->client->request(
            'DELETE',
            '/profile/website/delete/' . $website->getId()
        );

        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('id' => $website->getId()));
        $this->assertEquals(0, count($website));
    }

    public function testDeleteWebsiteFailed()
    {
        $this->client->request(
            'DELETE',
            '/profile/website/delete/' . 1000000
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditWebsiteSuccess()
    {
        $id = $this->createWebsite();
        $this->client->request(
            'PUT',
            '/profile/website/save/' . $id['id_w'],
            array(
                'websites' =>
                    array('name' => 'Vk', 'url' => 'awesome.com', 'status' => 'down',
                        'owner' => array('id' => $id['id_u'])))
        );

        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'Vk'));
        $this->assertEquals(1, count($website));
    }

    //Invalid data which allows to rendering page. Where we can see the message that such value already used.
    //Test will check contains response content such message or not.
    public function testEditUserFailed()
    {
        $id = $this->createWebsite();
        $this->client->request(
            'PUT',
            '/profile/website/save/' . $id['id_w'],
            array(
                'websites' =>
                    array('name' => 'demo', 'url' => 'awesome.com', 'status' => 'down',
                        'owner' => array('id' => $id['id_u'])))
        );
        $this->assertContains('<li>This value is already used.</li>', $this->client->getResponse()->getContent());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $websites = $this->em
            ->createQuery("SELECT w FROM AppBundle:Websites w WHERE w.name NOT IN ('demo', 'bluz')")
            ->getResult();

        foreach ($websites as $website) {
            if ($website->getName() == 'site') {
                self::bootKernel();
                $container = static::$kernel->getContainer()
                    ->get('service_container')
                ;
                $checkManipulate = new PrepareDataToManipulateCheck($container);
                $deleteCheck = new DeleteCheck($container);
                $deleteCheck->delete($checkManipulate->getCheckId($website));
            }
            $this->em->remove($website);
        }
        $this->em->flush();
        $this->em->close();
    }
}

