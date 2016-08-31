<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/20/15
 * Time: 4:37 PM
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Form\WebsitesType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Websites;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class WebsitesUserControllerTest extends WebTestCase
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

    //Checks notification with flag 0. (On profile user checking websites)
    public function testSaveNotificationCheckedSuccess()
    {
        $this->client->request(
            'POST',
            '/profile/website/notification/save',
            array('flag' => 0, 'user' => 136, 'website' => array(77,78))
        );

        $rows = $this->em->getRepository('AppBundle:WebsitesUser')->findBy(array('user' => 136));
        $this->assertEquals(2, count($rows));
    }

    //Checks notification with flag 1. (Drag&Drop panel)
    public function testSaveNotificationSuccess()
    {
        $this->client->request(
            'POST',
            '/profile/website/notification/save',
            array('flag' => 1, 'user' => array('admin', 'user'), 'website' => 77)
        );

        $rows = $this->em->getRepository('AppBundle:WebsitesUser')->findBy(array('website' => 77));
        $this->assertEquals(2, count($rows));
    }

    //Send to method unreal website id. Got response 404 (Not found)
    public function testSaveNotificationFailed()
    {
        $this->client->request(
            'POST',
            '/profile/website/notification/save',
            array('flag' => 1, 'user' => array(136, 212), 'website' => 23)
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    //Send to method unreal website id. Got response 404 (Not found)
    public function testSaveNotificationCheckedFailed()
    {
        $this->client->request(
            'POST',
            '/profile/website/notification/save',
            array('flag' => 0, 'user' => 136, 'website' => array(23, 78))
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $websites = $this->em->getRepository('AppBundle:WebsitesUser')->findAll();
        foreach ($websites as $website) {
            $this->em->remove($website);
        }
        $this->em->flush();
    }
}

