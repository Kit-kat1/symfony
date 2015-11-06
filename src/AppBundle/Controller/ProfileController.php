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
//        $em = $this->getDoctrine()->getManager();
//        $notify = $em->createQuery('SELECT IDENTITY(wu.website) FROM AppBundle:WebsitesUser wu WHERE wu.user = :id')
//            ->setParameter('id', $this->getUser())
//            ->getResult();
        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->render('admin2/profile.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'notifying' => $notify));
    }
}