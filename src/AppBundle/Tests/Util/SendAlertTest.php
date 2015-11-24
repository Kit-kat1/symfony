<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/23/15
 * Time: 3:26 PM
 */

namespace SendAlertTest;

use AppBundle\Entity\Users;
use AppBundle\Entity\Websites;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Util\SendAlert;
use AppBundle\Entity\WebsitesUser;

class SendAlertTest extends KernelTestCase
{
    /**
     * @var object
     */
    private $container;
    private $em;
    private $doctrine;

    public function __construct()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer()->get('service_container');
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->doctrine = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testGetUsersForAlerting()
    {
        $user = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Users')
            ->findOneBy(array('id' => 136));

        $website =  $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Websites')
            ->findOneBy(array('id' => 77));
        $website->setStatus(Websites::UP);

        $wu = new WebsitesUser();
        $wu->setUser($user);
        $wu->setWebsite($website);
        $wu->setNotify(1);

        $this->doctrine->persist($wu);
        $this->doctrine->flush();

        $sendAlert = new SendAlert($this->em, $this->container);

        $users = $sendAlert->getUsersForAlerting($website->getUrl());

        $this->assertEquals(1, count($users));
    }

    public function testSendMail()
    {
        $firstSite = $this->getMock('\AppBundle\Entity\Websites');
        $firstSite->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('firstSite.com'));

        $secondSite = $this->getMock('\AppBundle\Entity\Websites');
        $secondSite->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('secondSite.com'));

        $thirdSite = $this->getMock('\AppBundle\Entity\Websites');
        $thirdSite->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('thirdSite.com'));

        $website = $this->getMock('\AppBundle\Entity\Websites');
        $website->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($this->logicalOr('firstSite.com', 'secondSite.com', 'thirdSite.com')));

        $websitesRepository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $websitesRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($website));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($websitesRepository));


        $websitesUp[] = array('firstSite.com', 'secondSite.com');
        $websitesDown[] = array('thirdSite.com');
//        $entityManager = $this
//            ->getMockBuilder('\Doctrine\ORM\EntityManager')
//            ->disableOriginalConstructor()
//            ->getMock();

        $viaMail = $this->getMockBuilder('AppBundle\Util\ViaMail')->disableOriginalConstructor()
            ->getMock();

        $viaMail->expects($this->exactly(2))
            ->method('send')
            ->with(
                [136, 'firstSite.com'],
                [136, 'secondSite.com']
            );

        $this->container->set('app.send_alert_via_mail', $viaMail);

        $sendAlert = new SendAlert($entityManager, $this->container);
        $sendAlert->sendMail($websitesDown, $websitesUp);
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

        $sendAlert = new SendAlert($entityManager, $this->container);
        $sendAlert->updateStatus($website, Websites::UP);
    }

}
