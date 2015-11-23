<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/20/15
 * Time: 5:53 PM
 */

namespace PrepareDataToManipulateCheckTest;

use AppBundle\Util\PrepareDataToManipulateCheck;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PrepareDataToManipulateCheckTest extends KernelTestCase
{
    /**
     * @var object
     */
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

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    public function testGetCheckIdSuccess()
    {
        $check = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();

        $check->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));
        $cont = $this->container->set('app.pingdom_get_checks', $check);

        $website = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Websites')
            ->findOneBy(array('name' => 'demo'));

        $ob = new PrepareDataToManipulateCheck($this->container->get('service_container'));
        $this->assertEquals(1887176, $ob->getCheckId($website));
    }

    public function testGetCheckIdFailed()
    {
        $check = $this->getMockBuilder('AppBundle\Util\GetChecks')->disableOriginalConstructor()
            ->getMock();

        $check->expects($this->once())
            ->method('getChecks')
            ->will($this->returnValue($this->data));
        $cont = $this->container->set('app.pingdom_get_checks', $check);

        $website = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Websites')
            ->findOneBy(array('name' => 'somesite'));

        $ob = new PrepareDataToManipulateCheck($this->container->get('service_container'));
        $this->assertEquals(0, $ob->getCheckId($website));
    }
}
