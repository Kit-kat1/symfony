<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/12/15
 * Time: 1:29 PM
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WebsitesStatusController extends Controller
{
    /**
     * @Route("/websites", name="websites")
     */
    public function allWebsitesAction()
    {
        $checks = $this->get('app.pingdom_get_checks')->getChecks();
        $this->get('app.pingdom_status_add')->updateStatus($checks);
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        return $this->render('admin2/websites.html.twig', array('websites' => $websites, 'user' => $this->getUser()));
    }

    /**
     * @Route("/websites/{status}", name="siteStatus")
     */
    public function websitesStatusAction($status)
    {
        $checks = $this->get('app.pingdom_get_checks')->getChecks();
        $this->get('app.pingdom_status_add')->updateStatus($checks);
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findBy(['status' => $status]);
        return $this->render('admin2/websites.html.twig', array('websites' => $websites, 'user' => $this->getUser()));
    }
}