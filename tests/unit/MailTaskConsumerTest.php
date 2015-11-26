<?php

use AppBundle\Util\MailTaskConsumer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PhpAmqpLib\Message\AMQPMessage;

class MailTaskConsumerTest extends \Codeception\TestCase\Test
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


    protected function _before()
    {
        // accessing container
        $this->serviceContainer = $this->getModule('Symfony2')->container;
        $this->em = $this->getModule('Doctrine2')->em;
    }

    public function testSend()
    {
        $mailer = $this->getMockBuilder('\Swift_Mailer')->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send');

        $this->serviceContainer->set('swiftmailer.mailer.default', $mailer);

        $message = array('id' => array(136), 'url' => 'bluz.gunko.php.nixsolutions.com');
        $msg = new AMQPMessage(
            serialize($message)
        );

        $mailConsumer = new MailTaskConsumer($this->serviceContainer->get('service_container'), $this->em);
        $mailConsumer->execute($msg);
    }
}