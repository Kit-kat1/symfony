<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use AppBundle\Form\UsersType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $phone = preg_replace('/[^0-9]/', '', $data['phoneNumber']);
        if ($request->getMethod() == 'POST') {
            $user = new Users();
        } else {
            $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                ->find($data['id']);
            $roles = $user->getRoles();
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
            if (!$user) {
                return new Response('There is no user with id = ' . $data['id']);
            }
        }
        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data);

        $roles = [];
        $dataRoles= explode(',', $data['roles']);
        foreach ($dataRoles as $role) {
            if (trim($role) != 'ROLE_USER') {
                $roles[] = $role;
            }
        }
        $user->setRoles($roles);
        $user->setUpdated();
        $user->setPhoneNumber($phone);
        $user->setEnabled($data['enabled']);
        $user->setSalt(md5(time()));

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
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
}