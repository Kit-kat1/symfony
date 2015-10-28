<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use AppBundle\Form\UsersType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Repository\UsersRepository;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UsersController extends Controller
{
    /**
     * @Route("/admin/user/edit/{id}", name="editUser")
     */
    public function updateUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);
        $method = 'put';
        return $this->render('admin2/edit.html.twig', array('user' => $user, 'method' => $method));
    }

    /**
     * @Route("/admin/user/delete/{id}", name="deleteUser")
     */
    public function deleteUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->render('admin2/admin.html.twig', array('users' => $users));
    }

//    /**
//     * @Route("/admin/user/save", name="saveUser")
//     */
    public function saveUserAction(Request $request)
    {
        var_dump($request->get('username'));
        die();
        $data = $request->request->all();
        $user = new Users();
        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data);

        $user->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->render('admin2/admin.html.twig', array('users' => $users));
    }

    /**
     * @Route("/admin/user/edit", name="createUser")
     */
    public function createUserAction()
    {
        $users = new Users();
        $method = 'post';
        return $this->render('admin2/edit.html.twig', array('user' => $users,'method' => $method));
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
        return $this->render('admin2/admin.html.twig', array('users' => $users));
    }
}