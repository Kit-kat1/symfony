<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/24/15
 * Time: 11:16 AM
 */

namespace MailTaskConsumerTest;

use AppBundle\Util\MailTaskConsumer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PhpAmqpLib\Message\AMQPMessage;

class MailTaskConsumerTest extends KernelTestCase
{
    /**
     * @var object
     */
    private $container;
    private $em;

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function testSend()
    {
        $mailer = $this->getMockBuilder('\Swift_Mailer')->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send');

        $this->container->set('swiftmailer.mailer.default', $mailer);

        $message = array('id' => array(136), 'url' => 'bluz.gunko.php.nixsolutions.com');
        $msg = new AMQPMessage(
            serialize($message)
        );

        $mailConsumer = new MailTaskConsumer($this->container->get('service_container'), $this->em);
        $mailConsumer->execute($msg);
    }
}