<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/4/15
 * Time: 5:17 PM
 */
namespace AppBundle\Controller;

use AppBundle\Entity\WebsitesUser;
use AppBundle\Form\WebsitesType;
use AppBundle\Entity\Websites;
use AppBundle\Form\WebsitesUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SendMailController extends Controller
{
    /**
     * @Route("/profile/send/message", name="sendMail")
     */
    public function sendMailAction()
    {
//        $message = \Swift_Message::newInstance();

        $checks = $this->get('app.pingdom_connect')->connect();
        $sites = $this->get('app.pingdom_websites_down')->sitesDown($checks);
        $this->get('app.pingdom_send_mail')->sendMail($sites);

//        foreach ($sites as $url) {
//            $website = $this->getDoctrine()->getRepository('AppBundle:Websites')->findOneBy(array('url' => $url));
//            if ($website->getStatus() != 'down') {
//                $em = $this->getDoctrine()->getManager();
//                $website->setStatus('down')
//                $em->persist($website);
//                $em->flush();
//                $em = $this->getDoctrine()->getEntityManager();
//                $users = $em->createQueryBuilder()
//                    ->select('IDENTITY(wu.user)')
//                    ->from('AppBundle:WebsitesUser', 'wu')
//                    ->innerJoin('AppBundle:Websites', 'w', 'WITH', 'wu.website = w.id')
//                    ->where('w.url = :url')
//                    ->setParameter('url', $url)
//                    ->getQuery()
//                    ->getResult();
//
//                foreach ($users as $id) {
//                    $message = \Swift_Message::newInstance();
//                    $user = $this->getDoctrine()->getRepository('AppBundle:Users')->find(array_shift($id));
//                    $headers = $message->getHeaders();
//                    $headers->addTextHeader('EMAILS', $user->getEmail());
////            $headers->addMailboxHeader('EMAILS', array('gunko.k@nixsolutions.com','hunter@nixsolutions.com',
////                'bashmach@nixsolutions.com'));
//                    $headers->addTextHeader('PROJECT', 'Monitoring system');
//
//                    $message->setSubject('Testing Spooling!')
//                        ->setFrom('Katynichka95@gmail.com')
//                        ->setTo('ekaterinagunko6@gmail.com')
//                        ->setBody(
//                            $this->renderView(
//                                'admin2/text.html.twig',
//                                array('site' => $url)
//                            ),
//                            'text/html'
//                        )
//                        ->setCharset('UTF-8');
//
//                    $mailer = $this->get('mailer');
//                    $mailer->send($message);
//                }
//            }
//        }




        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->redirectToRoute('profile', ['user' => $this->getUser(), 'websites' => $websites,
            'notifying' => $notify]);
    }
}