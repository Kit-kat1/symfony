<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/16/15
 * Time: 1:54 PM
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthController extends Controller
{
//    /**
//     * @Route("/login")
//     */
//    public function loginAction()
//    {
//        if ($this->get('request')->attributes->has(Security::AUTHENTICATION_ERROR)) {
//            $error = $this->get('request')->attributes->get(Security::AUTHENTICATION_ERROR);
//        } else {
//            $error = $this->get('request')->getSession()->get(Security::AUTHENTICATION_ERROR);
//        }
//
//        return $this->render('admin2/login.html.twig', array(
//            'last_username' => $this->get('request')->getSession()->get(Security::LAST_USERNAME),
//            'error' => $error
//        ));
//    }
//    /**
//     * @Route("/login_check")
//     */
//    public function loginCheckAction()
//    {
//    }
}