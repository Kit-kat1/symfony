<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;

class SendAlert
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param $website
     * @param $status
     */
    public function updateStatus(Websites $website, $status)
    {
        $website->setStatus($status);
        $this->em->persist($website);
        $this->em->flush();
    }

    /**
     * @param $url
     * @return array
     */
    public function getUsersForAlerting($url)
    {
        $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('url' => $url));
        if ($website->getStatus() != Websites::DOWN) {
            $this->updateStatus($website, Websites::DOWN);
            $users = $this->em->createQueryBuilder()
                ->select('IDENTITY(wu.user)')
                ->from('AppBundle:WebsitesUser', 'wu')
                ->innerJoin('AppBundle:Websites', 'w', 'WITH', 'wu.website = w.id')
                ->where('w.url = :url')
                ->setParameter('url', $url)
                ->getQuery()
                ->getResult();
            return $users;
        }
        return [];
    }

    /**
     * @param $urlsDown
     * @param $urlsUp
     */
    public function sendMail($urlsDown, $urlsUp)
    {
        foreach ($urlsDown as $url) {
            $users = $this->getUsersForAlerting($url);
            foreach ($users as $id) {
                $this->container->get('app.send_alert_via_mail')->send($id, $url);
            }
        }

        foreach ($urlsUp as $url) {
            $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('url' => $url));
            if ($website->getStatus() == Websites::DOWN) {
                $this->updateStatus($website, Websites::UP);
            }
        }
    }
}

