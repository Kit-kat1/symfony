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
     * @Route("/signup", name="signup")
     */
    public function signUpAction()
    {
        return $this->render('admin2/signup.html.twig');
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('admin2/index.html.twig');
    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showDashboard()
    {
        return $this->render('admin2/dashboard.html.twig');
    }

    /**
     * @Route("/signin")
     */
    public function showSignInAction()
    {
        return $this->render('admin2/login.html.twig');
    }
}