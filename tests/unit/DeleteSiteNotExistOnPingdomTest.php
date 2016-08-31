<?php

use AppBundle\Entity\Websites;
use AppBundle\Util\DeleteSiteNotExistOnPingdom;

class DeleteSiteNotExistOnPingdomTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $em;
    protected $tester;
    private $serviceContainer;
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
        $this->serviceContainer->enterScope('request');
        $this->em = $this->getModule('Doctrine2')->em;
    }

    //Count websites which has been deleted during comparing db sites and sites on pingdom
    public function testDeleteSuccess()
    {
        $website = new Websites();
        $website->setUpdated();
        $website->setName('Some site');
        $website->setUrl('awesome.com');
        $user = $this->serviceContainer->get('doctrine')->getManager()->getRepository('AppBundle:Users')
            ->findOneBy(array('username' => 'admin'));
        $website->setOwner($user);
        $website->setStatus('up');

        $this->em->persist($website);
        $this->em->flush();

        $service = new DeleteSiteNotExistOnPingdom($this->serviceContainer->get('doctrine.orm.entity_manager'));
        $this->assertEquals(1, $service->delete($this->data));
    }
}