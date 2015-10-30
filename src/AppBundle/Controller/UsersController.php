<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use AppBundle\Form\UsersType;
use ApiBundle\Controller\TrackingController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Repository\UsersRepository;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UsersController extends Controller
{
    /**
     * Render page for editing user with send to page users data
     * @Route("/admin/user/edit/{id}", name="editUser")
     */
    public function updateUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);
        if (!$user) {
//            $this->get('session')->getFlashBag()->add('failed', 'There is no user with id = ' . $id);
//            $url = $this->generateUrl('admin');
//
//            return $this->redirect($url);
            return new Response('There is no user with id = ' . $id);
        }
        return $this->render('admin2/edit.html.twig', array('user' => $user, 'method' => 'PUT'));
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
        return $this->redirectToRoute('admin', ['users' => $users]);
    }

    public function saveUserAction(Request $request)
    {
        $data = $request->request->all();
        if ($request->getMethod() == 'POST') {
            $user = new Users();
        } else {
            $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                ->find($data['id']);
            if (!$user) {
                return new Response('There is no user with id = ' . $data['id']);
            }
        }
        $user->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->redirectToRoute('admin', ['users' => $users]);
    }

    /**
     * @Route("/admin/user/edit", name="createUser")
     */
    public function createUserAction()
    {
        $users = new Users();
        return $this->render('admin2/edit.html.twig', array('user' => $users, 'method' => 'POST'));
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
        $tracking = new TrackingController();
        $checks = $tracking->checkAction();
        $websites = count($checks);
        $up = 0;
        $down = 0;

        foreach ($checks as $check) {
            if ($check['status'] == 'up') {
                $up++;
            } elseif ($check['status'] == 'down') {
                $down++;
            }
        }

        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'up' => $up, 'down' => $down));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function showAdminAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->render('admin2/admin.html.twig', array('users' => $users, 'user' => $this->getUser()));
    }
}