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
//        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
//            ->findOneBy(array('user' => 63));
//        var_dump($notify);die();
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->render('admin2/profile.html.twig', array('user' => $this->getUser(), 'websites' => $websites));
    }
}