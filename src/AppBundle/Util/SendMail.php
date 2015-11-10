<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;

class SendMail
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
     */
    public function updateStatus($website)
    {
        $website->setStatus('down');
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
            $this->updateStatus($website);
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

    /**
     * @param $sites
     */
    public function sendMail($sites)
    {
        foreach ($sites as $url) {
            $users = $this->getUsersForAlerting($url);
            foreach ($users as $id) {
                $this->send($id, $url);
            }
        }
    }
}

