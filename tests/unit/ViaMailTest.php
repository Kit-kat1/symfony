<?php

use AppBundle\Util\ViaMail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ViaMailTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var object
     */
    private $serviceContainer;

    protected function _before()
    {
        // accessing container
        $this->serviceContainer = $this->getModule('Symfony2')->container;
    }

    public function testSend()
    {
        $sendMail = $this->getMockBuilder('\OldSound\RabbitMqBundle\RabbitMq\Producer')->disableOriginalConstructor()
            ->getMock();

        $sendMail->expects($this->once())
            ->method('publish');

        $this->serviceContainer->set('old_sound_rabbit_mq.add_mail_task_producer', $sendMail);

        $ob = new ViaMail($this->serviceContainer->get('service_container'));
        $ob->send(136, 'bluz.gunko.php.nixsolutions.com');
    }
}