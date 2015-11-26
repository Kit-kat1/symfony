<?php

use AppBundle\Entity\Users;
use AppBundle\Entity\Websites;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Util\SendAlert;
use AppBundle\Entity\WebsitesUser;

class SendAlertTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var object
     */
    private $serviceContainer;
    private $em;
    private $doctrine;

    protected function _before()
    {
        // accessing container
        $this->serviceContainer = $this->getModule('Symfony2')->container;
        $this->doctrine = $this->serviceContainer->get('doctrine')->getManager();
        $this->em = $this->getModule('Doctrine2')->em;
    }

    public function testGetUsersForAlerting()
    {
        $user = $this->doctrine->getRepository('AppBundle:Users')
            ->findOneBy(array('id' => 136));

        $website =  $this->doctrine->getRepository('AppBundle:Websites')
            ->findOneBy(array('id' => 77));
        $website->setStatus(Websites::UP);

        $wu = new WebsitesUser();
        $wu->setUser($user);
        $wu->setWebsite($website);
        $wu->setNotify(1);

        $this->doctrine->persist($wu);
        $this->doctrine->flush();

        $sendAlert = new SendAlert($this->em, $this->serviceContainer);

        $users = $sendAlert->getUsersForAlerting($website->getUrl());

        $this->assertEquals(2, count($users));
    }

    public function testUpdateStatus()
    {
        $website = $this->getMock('\AppBundle\Entity\Websites');
        $website->expects($this->once())
            ->method('setStatus')
            ->with(Websites::UP);

        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $sendAlert = new SendAlert($entityManager, $this->serviceContainer);
        $sendAlert->updateStatus($website, Websites::UP);
    }
}