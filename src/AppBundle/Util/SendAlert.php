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
    public function updateStatus($website, $status)
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
        if ($website->getStatus() != 'down') {
            $this->updateStatus($website, 'down');
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
     * @param $sitesDown
     * @param $sitesUp
     */
    public function sendMail($sitesDown, $sitesUp)
    {
        foreach ($sitesDown as $url) {
            $users = $this->getUsersForAlerting($url);
            foreach ($users as $id) {
                $this->container->get('app.send_alert_via_mail')->send($id, $url);
            }
        }

        foreach ($sitesUp as $url) {
            $website = $this->em->getRepository('AppBundle:Websites')->findOneBy(array('url' => $url));
            if ($website->getStatus() == 'down') {
                $this->updateStatus($website, 'up');
            }
        }
    }
}

