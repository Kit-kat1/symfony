<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/24/15
 * Time: 9:39 AM
 */

namespace ViaMailTest;

use AppBundle\Util\ViaMail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ViaMailTest extends KernelTestCase
{
    /**
     * @var object
     */
    private $container;

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    public function testSend()
    {
        $sendMail = $this->getMockBuilder('\OldSound\RabbitMqBundle\RabbitMq\Producer')->disableOriginalConstructor()
            ->getMock();

        $sendMail->expects($this->once())
            ->method('publish');

        $this->container->set('old_sound_rabbit_mq.add_mail_task_producer', $sendMail);

        $ob = new ViaMail($this->container->get('service_container'));
        $ob->send(136, 'bluz.gunko.php.nixsolutions.com');
    }
}
