<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/4/15
 * Time: 5:24 PM
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function showAdminAction()
    {
        //Gives access only for user with role SUPER ADMIN
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $users = $this->getDoctrine()->getEntityManager()
            ->createQuery("SELECT u FROM AppBundle:Users u WHERE u.username != 'admin'")
            ->getResult();
        return $this->render('admin2/admin.html.twig', array('users' => $users, 'user' => $this->getUser()));
    }


}