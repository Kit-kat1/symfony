<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Form\UsersType;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UsersController extends Controller
{
    /**
     * @Route("/admin/update/user", name="updateUser")
     */
    public function updateUserAction()
    {
        return $this->render('admin2/update.html.twig', array('user' => $this->getUser()));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('admin2/header.html.twig', array('user' => $this->getUser()));
    }

    /**
     * @Route("/input")
     */
    public function inputAction()
    {
        return $this->render('admin2/input.html');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showDashboardAction()
    {
        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser()));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function showAdminAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->render('admin2/admin.html.twig', array('user' => $this->getUser(), 'users' => $users));
    }
}