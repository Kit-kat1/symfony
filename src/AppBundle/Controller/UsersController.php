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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UsersController extends Controller
{
    /**
     * Render page for editing user with send to page users data
     * @Route("/admin/user/edit/{id}", name="editUser")
     * @Method({"GET"})
     */
    public function updateUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);
        if (!$user) {
            return new Response('There is no user with id = ' . $id);
        }
        $roles[] = implode(', ', $user->getRoles());
        $user->setRoles($roles);
        $form = $this->createForm(new UsersType(), $user);
        return $this->render('admin2/edit.html.twig', array(
            'form' => $form->createView(), 'user' => $this->getUser(), 'id' => $user->getId(),
            'method' => 'PUT'
        ));
    }

    /**
     * @Route("/admin/user/delete", name="deleteUser")
     * @Method({"DELETE"})
     */
    public function deleteUserAction(Request $request)
    {
        $data = $request->request->all();
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($data['id']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->redirectToRoute('admin', ['users' => $users]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Method({"POST"})
     */
    public function createUserSaveAction(Request $request)
    {
        $data = $request->request->all();

        $phone = preg_replace('/[^0-9]/', '', $data['users']['phoneNumber']);
        $user = new Users();


//        $roles = [];
        $dataRoles= explode(',', $data['users']['roles'][0]);
        $dataRoles = array_unique($dataRoles);
//        foreach ($dataRoles as $role) {
//            if (trim($role) != 'ROLE_USER') {
//                $roles[] = $role;
//            }
//        }
        $user->setRoles($dataRoles);
        $user->setUpdated();
        $user->setPhoneNumber($phone);
        if (isset($data['users']['enabled'])) {
            $user->setEnabled($data['users']['enabled']);
        }
        $user->setEnabled(0);
        $user->setSalt(md5(time()));

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        $data['users']['phoneNumber'] = $phone;

        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data['users']);

        $validator = $this->container->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
//            var_dump($errors);die();
            return $this->render('admin2/edit.html.twig', array(
                'form' => $form->createView(), 'user' => $this->getUser(), 'method' => 'POST'
            ));
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->redirectToRoute('admin', ['users' => $users]);
    }


    /**
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Method({"PUT"})
     */
    public function editUserSaveAction($id, Request $request)
    {
        $data = $request->request->all();

        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);

        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $user->removeRole($role);
        }
        if (!$user) {
            return new Response('There is no user with id = ' . $id);
        }

//        $roles = [];
        $dataRoles= explode(',', $data['users']['roles'][0]);
        $dataRoles = array_unique($dataRoles);
//        foreach ($dataRoles as $role) {
//            if (trim($role) != 'ROLE_USER') {
//                $roles[] = $role;
//            }
//        }
        $user->setRoles($dataRoles);
        $user->setUpdated();
        $phone = preg_replace('/[^0-9]/', '', $data['users']['phoneNumber']);
        $user->setPhoneNumber($phone);
        if (isset($data['users']['enabled'])) {
            $user->setEnabled($data['users']['enabled']);
        }
        $data['users']['phoneNumber'] = $phone;
        $user->setEnabled(0);
        $user->setSalt(md5(time()));

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data['users']);

        $validator = $this->container->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->render('admin2/edit.html.twig', ['form' => $form->createView(), 'user' => $this->getUser(),
                    'id' => $user->getId(), 'method' => 'PUT']);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->redirectToRoute('admin', ['users' => $users]);
    }

    /**
     * @Route("/admin/user/edit", name="createUser")
     * @Method({"GET"})
     */
    public function createUserAction()
    {
        $user = new Users();
        $form = $this->createForm(new UsersType(), $user);
        return $this->render('admin2/edit.html.twig', array(
            'form' => $form->createView(), 'user' => $this->getUser(), 'method' => 'POST'
        ));
    }
}