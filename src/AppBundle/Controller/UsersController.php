<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use AppBundle\Form\UsersType;
use FOS\RestBundle\Util\Codes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/admin/user/delete/{id}", name="deleteUser")
     * @Method({"DELETE"})
     */
    public function deleteUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);

        if ($user == null) {
            $response = new Response();
            return $response->setStatusCode(Codes::HTTP_NOT_FOUND);
        }

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

        $user = new Users();

        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data['users']);

        $user->setRoles(array_unique(explode(', ', $data['users']['roles'][0])));
        $user->setUpdated();

        if (isset($data['users']['enabled'])) {
            $user->setEnabled($data['users']['enabled']);
        }
        $user->setSalt(md5(time()));

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            return $this->render('admin2/edit.html.twig', array(
                'form' => $form->createView(), 'user' => $this->getUser(), 'method' => 'POST'
            ));
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

        if ($user == null) {
            $response = new Response();
            return $response->setStatusCode(Codes::HTTP_NOT_FOUND);
        }

        $roles = $user->getRoles();
        $Roles[] = implode(', ', $user->getRoles());
        $user->setRoles($Roles);

        $form = $this->createForm(new UsersType(), $user);
        $form->submit($data['users']);

        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $user->removeRole($role);
        }
        if (!$user) {
            return new Response('There is no user with id = ' . $id);
        }

        $user->setRoles(array_unique(explode(', ', $data['users']['roles'][0])));

        $user->setUpdated();
        if (isset($data['users']['enabled'])) {
            $user->setEnabled($data['users']['enabled']);
        }

        $user->setSalt(md5(time()));

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            return $this->render('admin2/edit.html.twig', ['form' => $form->createView(), 'user' => $this->getUser(),
                'id' => $user->getId(), 'method' => 'PUT']);
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->redirectToRoute('admin', ['users' => $users]);
    }

    /**
     * @Route("/admin/user/create", name="createUser")
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