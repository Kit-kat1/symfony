<?php

namespace AppBundle\Util;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Handler\StreamHandler;
use AppBundle\Entity\Users;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

class MailTaskConsumer implements ConsumerInterface
{
    private $container;
    private $em;
    private $logger;

    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
        $this->logger = new Logger('mails_tasks');
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            $this->logger->addInfo('Start executing');
            $data = unserialize($msg->body);


            $message = \Swift_Message::newInstance();
            $user = $this->em->getRepository('AppBundle:Users')->find(array_shift($data['id']));
            $headers = $message->getHeaders();
            $headers->addTextHeader('EMAILS', $user->getEmail());
            $headers->addTextHeader('PROJECT', 'Monitoring system');

            $message->setSubject('Website has fallen')
                ->setFrom('SendAlert@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->container->get('templating')->render(
                        'admin2/message.html.twig',
                        array('site' => $data['url'])
                    ),
                    'text/html'
                )
                ->setCharset('UTF-8');

            $mailer = $this->container->get('mailer');
            $mailer->send($message);
            $this->logger->addInfo('End executing');
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }
    }
}
