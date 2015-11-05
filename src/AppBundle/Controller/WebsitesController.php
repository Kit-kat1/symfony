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

class WebsitesController extends Controller
{
    /**
     * @Route("/profile/website/edit{id}", name="editWebsite")
     */
    public function editWebsiteAction($id)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);
        if (!$website) {
            return new Response('There is no website with id = ' . $id);
        }
        return $this->render('admin2/websiteEdit.html.twig', array('user' => $this->getUser(), 'website' => $website,
            'method' => 'PUT', 'users' => $users));
    }

    /**
     * @Route("/profile/website/delete/{id}", name="deleteWebsite")
     */
    public function deleteWebsiteAction($id)
    {
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($website);
        $em->flush();

        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        return $this->redirectToRoute('profile', ['websites' => $websites]);
    }

    public function saveWebsiteAction(Request $request)
    {
        $data = $request->request->all();
        if ($request->getMethod() == 'POST') {
            $website = new Websites();
        } else {
            $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
                ->find($data['id']);
            if (!$website) {
                return new Response('There is no user with id = ' . $data['id']);
            }
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($data['owner']);
        $website->setOwner($user);

        $form = $this->createForm(new WebsitesType(), $website);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($website);
        $em->flush();

        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        return $this->redirectToRoute('profile', ['websites' => $websites]);
    }

    /**
     * @Route("/profile/website/edit", name="createWebsite")
     */
    public function createWebsiteAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
        $website = new Websites();
        return $this->render('admin2/websiteEdit.html.twig', array('user' => $this->getUser(), 'website' => $website,
            'method' => 'POST', 'users' => $users));
    }

    /**
     * @Route("/websites", name="websites")
     */
    public function allWebsitesAction()
    {
        $checks = $this->get('app.pingdom_connect')->connect();
        $this->get('app.pingdom_status_add')->updateStatus($checks);
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        return $this->render('admin2/websites.html.twig', array('websites' => $websites, 'user' => $this->getUser()));
    }

    /**
     * @Route("/websites/{status}", name="siteStatus")
     */
    public function websitesStatusAction($status)
    {
        $checks = $this->get('app.pingdom_connect')->connect();
        $this->get('app.pingdom_status_add')->updateStatus($checks);
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findBy(['status' => $status]);
        return $this->render('admin2/websites.html.twig', array('websites' => $websites, 'user' => $this->getUser()));
    }
}