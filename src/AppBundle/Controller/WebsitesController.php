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
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WebsitesController extends Controller
{
    /**
     * @Route("/profile/website/edit{id}", name="editWebsite")
     */
    public function websiteEditAction($id)
    {
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);
        if (!$website) {
            return new Response('There is no website with id = ' . $id);
        }
        return $this->render('admin2/websiteEdit.html.twig', array('website' => $website, 'method' => 'PUT'));
    }

    /**
     * @Route("/profile/website/delete/{id}", name="deleteWebsite")
     */
    public function deleteUserAction($id)
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
    public function createUserAction()
    {
        $website = new Websites();
        return $this->render('admin2/websiteEdit.html.twig', array('website' => $website, 'method' => 'POST'));
    }
}