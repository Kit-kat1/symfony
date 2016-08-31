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
    private $container;
    private $data = array('checks' =>
        array(
            0 =>
                array( 'id' => 1861126, 'created' => 1446200585, 'name' => 'Bluz tracking',
                    'hostname' => 'bluz.gunko.php.nixsolutions.com', 'use_legacy_notifications' => true,
                    'resolution' => 1, 'type' => 'http', 'ipv6' => false, 'lasterrortime' => 1448178819,
                    'lasttesttime' =>  1448274421, 'lastresponsetime' =>  458, 'status' =>  'up',
                    'probe_filters' => array ()),
            1 =>
                array('id' =>  1883166, 'created' =>  1447833933, 'name' =>  'sanon',
                    'hostname' =>  'sanon280.citynet.kharkov.ua', 'use_legacy_notifications' =>  false,
                    'resolution' =>  5, 'type' =>  'http', 'ipv6' =>  false, 'lasterrortime' =>  1447947175,
                    'lasttesttime' =>  1448274172, 'lastresponsetime' =>  373, 'status' =>  'up',
                    'probe_filters' => array ()),
            2 =>
                array ( 'id' =>  1887176, 'created' =>  1448034991, 'name' =>  'demo',
                    'hostname' =>  'demo.gunko.php.nixsolutions.com', 'use_legacy_notifications' =>  false,
                    'resolution' =>  5, 'type' =>  'http', 'ipv6' =>  false, 'lasterrortime' =>  1448274188,
                    'lasttesttime' =>  1448274188, 'lastresponsetime' =>  0, 'status' =>  'down',
                    'probe_filters' => array ())));

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
        $this->container = static::$kernel->getContainer();
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testCreateWebsiteSuccess()
    {
        $createCheck = $this->getMockBuilder('AppBundle\Util\CreateCheck')->disableOriginalConstructor()
            ->getMock();

        $createCheck->expects($this->once())
            ->method('create')
            ->will($this->returnValue(array('id' => 214358, 'name' => 'site')))
            ->with("name=site&type=http&host=mysite.com");
        $this->client->getContainer()->set('app.pingdom_create_new_check', $createCheck);

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
        $createCheck = $this->getMockBuilder('AppBundle\Util\CreateCheck')->disableOriginalConstructor()
            ->getMock();

        $createCheck->expects($this->exactly(0))
            ->method('create')
            ->will($this->returnValue(array('id' => 214358, 'name' => 'Newsite')));
        $this->client->getContainer()->set('app.pingdom_create_new_check', $createCheck);

        $this->client->request(
            'POST',
            '/profile/website/save',
            array(
                'websites' =>
                    array('name' => 'Newsite', 'url' => 'bluz.gunko.php.nixsolutions.com', 'status' => 'down'))
        );

        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'Newsite'));
        $this->assertEquals(0, count($website));
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
        $getChecks = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();
        $deleteCheck = $this->getMockBuilder('AppBundle\Util\DeleteCheck')->disableOriginalConstructor()
            ->getMock();

        $getChecks->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));
        $deleteCheck->expects($this->once())
            ->method('delete')
            ->will($this->returnValue('Deleted successful.'));

        $this->client->getContainer()->set('app.pingdom_delete_check', $deleteCheck);
        $this->client->getContainer()->set('app.pingdom_get_checks', $getChecks);

        $Ids = $this->createWebsite();

        $this->client->request(
            'DELETE',
            '/profile/website/delete/' . $Ids['id_w']
        );

        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('id' => $Ids['id_w']));
        $this->assertEquals(0, count($website));
    }

    public function testDeleteWebsiteFailed()
    {
        $deleteCheck = $this->getMockBuilder('AppBundle\Util\DeleteCheck')->disableOriginalConstructor()
            ->getMock();

        $deleteCheck->expects($this->exactly(0))
            ->method('delete')
            ->will($this->returnValue(array('error' => array('statuscode' => 404, 'errormessage' => 'Not found'))));
        $this->client->getContainer()->set('app.pingdom_delete_check', $deleteCheck);

        $this->client->request(
            'DELETE',
            '/profile/website/delete/' . 1000000
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditWebsiteSuccess()
    {
        $getChecks = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();
        $updateCheck = $this->getMockBuilder('AppBundle\Util\EditCheck')->disableOriginalConstructor()
            ->getMock();

        $getChecks->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));
        $updateCheck->expects($this->once())
            ->method('update')
            ->will($this->returnValue(array('id' => 2238473, 'name' => 'Vk')));

        $this->client->getContainer()->set('app.pingdom_edit_check', $updateCheck);
        $this->client->getContainer()->set('app.pingdom_get_checks', $getChecks);

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
    public function testEditWebsiteFailed()
    {
        $getChecks = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();
        $updateCheck = $this->getMockBuilder('AppBundle\Util\EditCheck')->disableOriginalConstructor()
            ->getMock();

        $getChecks->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));
        $updateCheck->expects($this->exactly(0))
            ->method('update')
            ->will($this->returnValue(array('error' => array('statuscode' => 404, 'errormessage' => 'Not found'))));

        $this->client->getContainer()->set('app.pingdom_edit_check', $updateCheck);
        $this->client->getContainer()->set('app.pingdom_get_checks', $getChecks);

        $id = $this->createWebsite();
        $this->client->request(
            'PUT',
            '/profile/website/save/' . $id['id_w'],
            array(
                'websites' =>
                    array('name' => 'demo', 'url' => 'awesome.com', 'status' => 'down',
                        'owner' => array('id' => $id['id_u'])))
        );
//        var_dump( $this->client->getResponse()->getContent());die();
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
            $this->em->remove($website);
        }
        $this->em->flush();
    }
}

