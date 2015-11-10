<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;

class ViaMail
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function send($id, $url)
    {
        $message = \Swift_Message::newInstance();
        $user = $this->em->getRepository('AppBundle:Users')->find(array_shift($id));
        $headers = $message->getHeaders();
        $headers->addTextHeader('EMAILS', $user->getEmail());
        $headers->addTextHeader('PROJECT', 'Monitoring system');

        $message->setSubject('Testing Spooling!')
            ->setFrom('SendAlert@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->container->get('templating')->render(
                    'admin2/text.html.twig',
                    array('site' => $url)
                ),
                'text/html'
            )
            ->setCharset('UTF-8');

        $mailer = $this->container->get('mailer');
        $mailer->send($message);
    }
}

