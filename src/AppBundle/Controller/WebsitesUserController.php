<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/4/15
 * Time: 5:17 PM
 */
namespace AppBundle\Controller;

use AppBundle\Entity\WebsitesUser;
use AppBundle\Entity\Websites;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
class WebsitesUserController extends Controller
{
    /**
     * @Route("/profile/website/notification/save", name="saveNotification")
     */
    public function saveNotificationAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if (!$data['dd']) {
            $user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($data['user']);
            $websites = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
                ->findBy(array('user' => $data['user']));

            foreach ($websites as $site) {
                $em->remove($site);
            }

            foreach ($data['website'] as $site) {
                $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
                    ->find($site);
                $sitesUser = new WebsitesUser();
                $sitesUser->setUser($user);
                $sitesUser->setWebsite($website);
                $sitesUser->setNotify(1);
                $em->persist($sitesUser);
            }
            $em->flush();
        } else {
            if (!array_key_exists('user', $data)) {
                $websites = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
                    ->findAll();

                foreach ($websites as $site) {
                    $em->remove($site);
                }
                $em->flush();
            } else {
                $users = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
                    ->findBy(array('website' => $data['website']));
                $website = $this->getDoctrine()->getRepository('AppBundle:Websites')->find($data['website']);

                foreach ($users as $user) {
                    $em->remove($user);
                }

                foreach ($data['user'] as $u) {
                    $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                        ->findOneBy(array('username' => $u));

                    $sitesUser = new WebsitesUser();
                    $sitesUser->setUser($user);
                    $sitesUser->setWebsite($website);
                    $sitesUser->setNotify(1);
                    $em->persist($sitesUser);
                }
                $em->flush();
            }
        }
        return $this->redirectToRoute('profile');
    }
}

