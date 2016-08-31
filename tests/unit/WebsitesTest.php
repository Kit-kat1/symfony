<?php

use FOS\RestBundle\Util\Codes;
use AppBundle\Entity\Websites;

class WebsitesTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var object
     */
    private $serviceContainer;
    private $em;
    private $doctrine;
    private $client;
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

    protected function _before()
    {
        // accessing container
        $this->serviceContainer = $this->getModule('Symfony2')->container;
        $this->em = $this->getModule('Doctrine2')->em;
        $this->doctrine = $this->serviceContainer->get('doctrine')->getManager();

        $this->client = $this->serviceContainer->get('test.client');
        $this->client->setServerParameters(array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));

    }

    public function createWebsite()
    {
        $owner = $this->doctrine->getRepository('AppBundle:Users')->findOneBy(array('username' => 'admin'));
        $userId = $this->tester->grabFromRepository('\AppBundle\Entity\Users', 'id', array('username' => 'admin'));

        $this->tester->persistEntity(new Websites(), array('name' => 'New site', 'url' => 'sitename.com',
            'status' => 'up', 'owner' => $owner));
        $websiteId = $this->tester->grabFromRepository('\AppBundle\Entity\Websites', 'id', array('name' => 'New site'));
        return ['id_w' => $websiteId, 'id_u' => $userId];
    }

    public function testCreateWebsiteSuccess()
    {
        $this->tester->amHttpAuthenticated('user', 'qwerty');

        $createCheck = $this->getMockBuilder('AppBundle\Util\CreateCheck')->disableOriginalConstructor()
            ->getMock();

        $createCheck->expects($this->once())
            ->method('create')
            ->will($this->returnValue(array('id' => 214358, 'name' => 'site')))
            ->with("name=site&type=http&host=mysite.com");
        $this->serviceContainer->set('app.pingdom_create_new_check', $createCheck);

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
        $this->tester->amHttpAuthenticated('user', 'qwerty');
        $mock = $this->getMockBuilder('AppBundle\Util\CreateCheck')->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->exactly(0))
            ->method('create')
            ->will($this->returnValue(array('id' => 214358, 'name' => 'Newsite')));
        $this->serviceContainer->set('app.pingdom_create_new_check', $mock);

        $this->tester->amOnPage('/profile/website/create');
        $this->tester->see('Create website');
        $this->tester->fillField('websites[name]', 'Newsite');
        $this->tester->fillField('websites[url]', 'bluz.gunko.php.nixsolutions.com');
        $this->tester->selectOption('select', 'down');
        $this->tester->click('button', '#createWebsite');
        $this->tester->see('This value is already used.');

        $website = $this->doctrine->getRepository('AppBundle:Websites')->findOneBy(array('name' => 'Newsite'));
        $this->assertEquals(0, count($website));
    }

    public function testDeleteWebsite()
    {
        $this->tester->amHttpAuthenticated('user', 'qwerty');
        $getChecks = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();
        $deleteCheck = $this->getMockBuilder('AppBundle\Util\DeleteCheck')->disableOriginalConstructor()
            ->getMock();

        $this->tester->wantTo('Delete website');
        $this->tester->amHttpAuthenticated('user', 'qwerty');
        $this->tester->amOnPage('/profile');
        $id = $this->createWebsite();

        $getChecks->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));

        $deleteCheck->expects($this->once())
            ->method('delete')
            ->will($this->returnValue('Deleted successful.'));

        $this->serviceContainer->set('app.pingdom_get_checks', $getChecks);
        $this->serviceContainer->set('app.pingdom_delete_check', $deleteCheck);
        $this->client->request('DELETE', '/profile/website/delete/' . $id['id_w']);

        $this->tester->dontSeeInRepository('\AppBundle\Entity\Websites', array('name' => 'New site'));
    }

    public function testDeleteWebsiteFailed()
    {
        $this->tester->wantTo('Delete unreal website and got exception "Not found"');
        $this->tester->amHttpAuthenticated('user', 'qwerty');
        $this->tester->amOnPage('/profile');
        $this->tester->sendAjaxRequest('DELETE', '/profile/website/delete/10000000');
        $this->tester->seeResponseCodeIs(Codes::HTTP_NOT_FOUND);
    }

    public function testEditWebsite()
    {
        $this->tester->amHttpAuthenticated('user', 'qwerty');
        $getCheckId = $this->getMockBuilder('AppBundle\Util\PrepareDataToManipulateCheck')->disableOriginalConstructor()
            ->getMock();
        $updateCheck = $this->getMockBuilder('AppBundle\Util\EditCheck')->disableOriginalConstructor()
            ->getMock();

        $getCheckId->expects($this->once())
            ->method('getCheckId')
            ->will($this->returnValue(1));
        $updateCheck->expects($this->once())
            ->method('update')
            ->will($this->returnValue(array('id' => 2238473, 'name' => 'Vk')));

        $this->serviceContainer->set('app.pingdom_check_manipulate', $getCheckId);
        $this->serviceContainer->set('app.pingdom_edit_check', $updateCheck);

        $id = $this->createWebsite();

        $this->client->request(
            'PUT',
            '/profile/website/save/' . $id['id_w'],
            array(
                'websites' =>
                    array('name' => 'Vk', 'url' => 'awesome.com', 'status' => 'down',
                        'owner' => array('id' => $id['id_u'])))
        );
        $this->tester->seeInRepository('\AppBundle\Entity\Websites', array('name' => 'Vk'));
    }
}