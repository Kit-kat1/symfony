<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Websites;
use AppBundle\Form\WebsitesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class WebsitesController extends Controller
{
    /**
     * @Route("/profile/website/edit/{id}", name="editWebsite")
     * @Method({"GET"})
     */
    public function editWebsiteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);
        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('website' => $id));

        $notNotify = $em->createQueryBuilder()
            ->select('u')
            ->from('AppBundle:Users', 'u')
            ->leftJoin('AppBundle:WebsitesUser', 'wu', 'WITH', 'wu.user = u.id AND wu.website = :id')
            ->where('wu.user IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if (!$website) {
            return new Response('There is no website with id = ' . $id);
        }
        $website->setUrl('http://' . $website->getUrl());
        $form = $this->createForm(new WebsitesType(), $website);
        return $this->render('admin2/websiteEdit.html.twig', array(
            'form' => $form->createView(),'user' => $this->getUser(), 'website' => $website,
            'method' => 'PUT', 'notNotify' => $notNotify, 'notify' => $notify));
    }

    /**
     * @Route("/profile/website/delete/{id}", name="deleteWebsite")
     * @Method({"DELETE"})
     */
    public function deleteWebsiteAction($id)
    {
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);

        if ($website == null) {
            $response = new Response();
            return $response->setStatusCode(404);
        }

        $checkId = $this->get('app.pingdom_check_manipulate')->getCheckId($website);
        $this->get('app.pingdom_delete_check')->delete($checkId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($website);
        $em->flush();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Method({"POST"})
     */
    public function createWebsiteSaveAction(Request $request)
    {
        $data = $request->request->all();

        $website = new Websites();

        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findOneBy(array('id' => $this->getUser()->getId()));

        $form = $this->createForm(new WebsitesType(), $website);
        $form->submit($data['websites']);

        $website->setOwner($user);

        if (!$form->isValid()) {
            return $this->render('admin2/websiteCreate.html.twig', array(
                'form' => $form->createView(), 'website' => $website, 'user' => $user, 'method' => 'POST'
            ));
        } else {
            //Create check in pingdom
            $body = $this->get('app.pingdom_check_manipulate')->getBody($data['websites']);
            $this->get('app.pingdom_create_new_check')->create($body);

            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();
        }

        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        return $this->redirectToRoute('profile', ['websites' => $websites, 'notifying' => $notify]);
    }

    /**
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Method({"PUT", "POST"})
     */
    public function editWebsiteSaveAction($id, Request $request)
    {
        $data = $request->request->all();

        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);
        $checkId = $this->get('app.pingdom_check_manipulate')->getCheckId($website);

        if ($website == null) {
            $response = new Response();
            return $response->setStatusCode(404);
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findOneBy(array('id' => $data['websites']['owner']));

        $form = $this->createForm(new WebsitesType(), $website);
        $form->submit($data['websites']);

        $website->setOwner($user);

        if (!$form->isValid()) {
            $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
                ->findBy(array('website' => $id));

            $em = $this->getDoctrine()->getManager();

            $notNotify = $em->createQueryBuilder()
                ->select('u')
                ->from('AppBundle:Users', 'u')
                ->leftJoin('AppBundle:WebsitesUser', 'wu', 'WITH', 'wu.user = u.id AND wu.website = :id')
                ->where('wu.user IS NULL')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();

            if (!$website) {
                return new Response('There is no website with id = ' . $id);
            }
            return $this->render('admin2/websiteEdit.html.twig', array(
                'form' => $form->createView(),'user' => $this->getUser(), 'website' => $website,
                'method' => 'POST', 'notNotify' => $notNotify, 'notify' => $notify));
        } else {
            $body = $this->get('app.pingdom_check_manipulate')->getBody($data['websites'], $checkId);
            $this->get('app.pingdom_edit_check')->update($checkId, $body);

            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();
        }
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
            ->findBy(array('user' => $this->getUser()));
        return $this->redirectToRoute('profile', ['websites' => $websites, 'notifying' => $notify]);
    }

    /**
     * @Route("/profile/website/edit", name="createWebsite")
     * @Method({"GET"})
     */
    public function createWebsiteAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
        $website = new Websites();
        $form = $this->createForm(new WebsitesType(), $website);
        return $this->render('admin2/websiteCreate.html.twig', array(
            'form' => $form->createView(), 'user' => $this->getUser(), 'website' => $website,
            'method' => 'POST', 'users' => $users));
    }
}
