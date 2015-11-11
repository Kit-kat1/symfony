<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/11/15
 * Time: 11:14 AM
 */
//namespace AppBundle\Listeners;
//
//use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
//use Symfony\Component\DependencyInjection\ContainerInterface;
//use AppBundle\Entity\Websites;
//
//class PostPersistInvite
//{
//    private $container;
//
//    public function __construct(ContainerInterface $container)
//    {
//        $this->container = $container;
//    }
//
//    public function postPersist(LifecycleEventArgs $args)
//    {
//        $entity = $args->getObject();
////        var_dump($entity);die();
//        if ($entity instanceof Websites) {
//            $message = serialize(array("id" => $entity->getId()));
//            $this->container->get("old_sound_rabbit_mq.add_mail_task_producer")
//                            ->publish($message);
//        }
//    }
//}