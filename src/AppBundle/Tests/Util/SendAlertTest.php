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

        $sendAlert = new SendAlert($this->em, $this->container);

        $users = $sendAlert->getUsersForAlerting($website->getUrl());

        $this->assertEquals(1, count($users));
    }

//    public function testSendMail()
//    {
//        $website = $this->getMock('\AppBundle\Entity\Websites');
//
//        $websitesRepository = $this
//            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $websitesRepository->expects($this->once())
//            ->method('findOneBy')
//            ->will($this->returnValue($website));
//
//        // Last, mock the EntityManager to return the mock of the repository
//        $entityManager = $this
//            ->getMockBuilder('\Doctrine\ORM\EntityManager')
//            ->disableOriginalConstructor()
//            ->setMethods(array('getRepository', 'createQueryBuilder', 'persist', 'from', 'where', 'innerJoin',
//                'SetParameter', 'getQuery', 'getResult', 'flush'))
//            ->getMock();
//
//        $entityManager->expects($this->any())
//            ->method('persist');
//        $entityManager->expects($this->any())
//            ->method('from');
//        $entityManager->expects($this->any())
//            ->method('where');
//        $entityManager->expects($this->any())
//            ->method('innerJoin');
//        $entityManager->expects($this->any())
//            ->method('SetParameter');
//        $entityManager->expects($this->any())
//            ->method('getQuery');
//        $entityManager->expects($this->any())
//            ->method('getResult');
//        $entityManager->expects($this->any())
//            ->method('flush');
//        $entityManager->expects($this->any())
//            ->method('getRepository')
//            ->will($this->returnValue($websitesRepository));
//        $entityManager->expects($this->any())
//            ->method('createQueryBuilder')
//            ->will($this->returnValue($websitesRepository));
//
//        $websitesUp[] = array('firstSite.com', 'thirdSite.com');
//        $websitesDown[] = array('secondSite.com');
//
//        $viaMail = $this->getMockBuilder('AppBundle\Util\ViaMail')->disableOriginalConstructor()
//            ->getMock();
//        $viaMail->expects($this->exactly(1))
//            ->method('send')
//            ->with(136, array('secondSite.com'));
//
//        $this->container->set('app.send_alert_via_mail', $viaMail);
//
//        $sendAlert = new SendAlert($entityManager, $this->container);
//        $sendAlert->sendMail($websitesDown, $websitesUp);
//    }

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
