<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;

class DeleteSiteNotExistOnPingdom
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $data
     * @return integer
     */
    public function delete($data)
    {
        $manager = $this->em->getRepository('AppBundle:Websites');

        $checks = $data['checks'];

        $web = $manager->findAll();

        $webStatus = [];
        foreach ($checks as $check) {
            $webStatus[] = array($check['hostname'] => $check['status']);
        }

        $url = [];
        foreach ($checks as $check) {
            $url[] = $check['hostname'];
        }

        $webUrl = [];
        foreach ($web as $website) {
            $webUrl[] = $website->getUrl();
        }

        $diff = array_diff($webUrl, $url);
        $cross = array_uintersect($url, $webUrl, "strcasecmp");

        $count = 0;
        foreach ($diff as $url) {
            $site = $manager->findOneBy(array('url' => $url));
            $this->em->remove($site);
            $count++;
        }
        $this->em->flush();

        return $count;
    }
}

