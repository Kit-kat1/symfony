<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/4/15
 * Time: 5:17 PM
 */
namespace AppBundle\Controller;

use AppBundle\Entity\WebsitesUser;
use AppBundle\Form\WebsitesType;
use AppBundle\Entity\Websites;
use AppBundle\Form\WebsitesUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile", name="profile")
     */
    public function userProfileAction()
    {
//        $notify = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
//            ->findOneBy(array('user' => 63));
//        var_dump($notify);die();
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->render('admin2/profile.html.twig', array('user' => $this->getUser(), 'websites' => $websites));
    }

    /**
     * @Route("/profile/website/notification/save", name="saveNotification")
     */
    public function saveNotificationAction(Request $request)
    {
        $data = $request->request->all();

        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($data['user']);
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($data['website']);
//        if ($request->getMethod() == 'POST') {
            $sitesUser = new WebsitesUser();
//        } else {
//            $sitesUser = $this->getDoctrine()->getRepository('AppBundle:Users')
//                ->find($data['id']);
//            $roles = $sitesUser->getRoles();
//            foreach ($roles as $role) {
//                $sitesUser->removeRole($role);
//            }
//            if (!$user) {
//                return new Response('There is no user with id = ' . $data['id']);
//            }
//        }
//        $form = $this->createForm(new WebsitesUserType(), $sitesUser);
//        $form->submit($data);

        $sitesUser->setUser($user);
        $sitesUser->setWebsite($website);
        $sitesUser->setNotify(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($sitesUser);
        $em->flush();

        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
        return $this->redirectToRoute('profile', ['websites' => $websites, 'user' => $this->getUser()]);
    }
}