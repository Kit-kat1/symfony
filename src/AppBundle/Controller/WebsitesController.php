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
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WebsitesController extends Controller
{
    /**
     * @Route("/profile/website/edit{id}", name="editWebsite")
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
        return $this->render('admin2/websiteEdit.html.twig', array('user' => $this->getUser(), 'website' => $website,
            'method' => 'PUT', 'notNotify' => $notNotify, 'notify' => $notify));
    }

    /**
     * @Route("/profile/website/delete/{id}", name="deleteWebsite")
     */
    public function deleteWebsiteAction($id)
    {
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);

        $checkId = $this->get('app.pingdom_check_manipulate')->getCheckId($website);
        $this->get('app.pingdom_delete_check')->delete($checkId);

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

            //Create check in pingdom
            $body = $this->get('app.pingdom_check_manipulate')->getBody($data);
            $this->get('app.pingdom_create_new_check')->create($body);

            $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                ->findOneBy(array('id' => $data['owner']));
        } else {
            $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
                ->find($data['id']);
            if (!$website) {
                return new Response('There is no user with id = ' . $data['id']);
            }

            //Update check in pingdom
            $checkId = $this->get('app.pingdom_check_manipulate')->getCheckId($website);
            $body = $this->get('app.pingdom_check_manipulate')->getBody($data, $data['id']);
            $this->get('app.pingdom_edit_check')->update($checkId, $body);

            $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                ->findOneBy(array('username' => $data['owner']));
        }

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
        return $this->render('admin2/websiteCreate.html.twig', array('user' => $this->getUser(), 'website' => $website,
            'method' => 'POST', 'users' => $users));
    }

    /**
     * @Route("/websites", name="websites")
     */
    public function allWebsitesAction()
    {
        $checks = $this->get('app.pingdom_get_checks')->getChecks();
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
        $checks = $this->get('app.pingdom_get_checks')->getChecks();
        $this->get('app.pingdom_status_add')->updateStatus($checks);
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findBy(['status' => $status]);
        return $this->render('admin2/websites.html.twig', array('websites' => $websites, 'user' => $this->getUser()));
    }
}