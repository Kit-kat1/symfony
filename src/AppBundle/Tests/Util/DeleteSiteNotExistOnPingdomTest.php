<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/23/15
 * Time: 1:41 PM
 */

namespace DeleteSiteNotExistOnPingdomTest;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Util\DeleteSiteNotExistOnPingdom;
use AppBundle\Entity\Websites;

class DeleteSiteNotExistOnPingdomTest extends KernelTestCase
{
    /**
     * @var object
     */
    private $container;
    private $em;
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

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    //Count websites which has been deleted during compareing db sites and sites on pingdom
    public function testDeleteSuccess()
    {
        $website = new Websites();
        $website->setUpdated();
        $website->setName('Some site');
        $website->setUrl('awesome.com');
        $user = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Users')
            ->findOneBy(array('username' => 'admin'));
        $website->setOwner($user);
        $website->setStatus('up');

        $this->container->get('doctrine')->getManager()->persist($website);
        $this->container->get('doctrine')->getManager()->flush();

        $service = new DeleteSiteNotExistOnPingdom($this->container->get('doctrine.orm.entity_manager'));
        $this->assertEquals(1, $service->delete($this->data));
    }
}
