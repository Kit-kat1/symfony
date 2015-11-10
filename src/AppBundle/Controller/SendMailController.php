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
        $checks = $this->get('app.pingdom_connect')->connect();
        $sites = $this->get('app.pingdom_websites_down')->sitesDown($checks);
        $this->get('app.pingdom_send_alert')->sendMail($sites);

        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->redirectToRoute('profile', ['user' => $this->getUser(), 'websites' => $websites,
            'notifying' => $notify]);
    }
}