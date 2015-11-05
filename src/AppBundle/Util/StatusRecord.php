<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;

class StatusRecord
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $data
     * @return array
     */
    public function updateStatus($data)
    {
        $manager = $this->em->getRepository('AppBundle:Websites');

        $checks = $data['checks'];
        // Check for errors returned by the API
        if (isset($response['error'])) {
            return [
                'error' => $response['error']['errormessage']
            ];
        }

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

        foreach ($diff as $url) {
            $site = $manager->findOneBy(array('url' => $url));
            $this->em->remove($site);
            $this->em->flush();
        }

        foreach ($webStatus as $siteStatus) {
            $url = array_keys($siteStatus);
            if (in_array($url[0], $cross)) {
                $site = $manager->findOneBy(array('url' => $url[0]));
                $status = array_values($siteStatus);
                $site->setStatus($status[0]);
                $this->em->persist($site);
                $this->em->flush();
            }
        }
        return [];
    }
}

