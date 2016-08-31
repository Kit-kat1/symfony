<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ViaMail
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function send($id, $url)
    {
        $msg = array('id' => $id, 'url' => $url);
        $this->container->get('old_sound_rabbit_mq.add_mail_task_producer')->publish(serialize($msg));
    }
}

