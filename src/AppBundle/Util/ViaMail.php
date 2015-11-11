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
        $msg = array('id' => $id, 'url' => $url);
        $this->container->get('old_sound_rabbit_mq.add_mail_task_producer')->publish(serialize($msg));
    }
}

