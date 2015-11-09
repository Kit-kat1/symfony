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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * @Route("/profile", name="profile")
     */
    public function userProfileAction()
    {
        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->render('admin2/profile.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'notifying' => $notify));
    }

    /**
     * @Route("/profile/send/message", name="sendMail")
     */
    public function sendMailAction()
    {
        $message = \Swift_Message::newInstance();

        $headers = $message->getHeaders();
        $headers->addMailboxHeader('EMAILS', array('gunko.k@nixsolutions.com','hunter@nixsolutions.com',
            'bashmach@nixsolutions.com'));
        $headers->addTextHeader('PROJECT', 'Monitoring system');

        $message->setSubject('Testing Spooling!')
            ->setFrom('Katynichka95@gmail.com')
            ->setTo('ekaterinagunko6@gmail.com')
            ->setBody(
                $this->renderView(
                    'admin2/text.html.twig'
                ),
                'text/html'
            )
            ->setCharset('UTF-8');

//        $headers->addTextHeader('EMAILS', "gunko.k@nixsolutions.com, hunter@nixsolutions.com,
//         bashmach@nixsolutions.com");

//        var_dump($message);die();
        $mailer = $this->get('mailer');

        $mailer->send($message);

        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->get('swiftmailer.transport.real');

        $spool->flushQueue($transport);

        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->redirectToRoute('profile', ['user' => $this->getUser(), 'websites' => $websites,
            'notifying' => $notify]);
    }
}