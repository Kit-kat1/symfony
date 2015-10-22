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
     * @Route("/create/user", name="createUser")
     */
    public function createUserAction()
    {

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
    public function showDashboard()
    {
        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser()));
    }
}