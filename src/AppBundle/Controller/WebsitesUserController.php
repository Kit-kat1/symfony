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
use Symfony\Component\HttpFoundation\Response;
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
        if (!$data['flag']) {
            $user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($data['user']);

            if ($user == null) {
                $response = new Response();
                return $response->setStatusCode(404);
            }
            $websites = $this->getDoctrine()->getRepository('AppBundle:WebsitesUser')
                ->findBy(array('user' => $data['user']));

            foreach ($websites as $site) {
                $em->remove($site);
            }

            $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')->findAll();
            $sites = count($data['website']);
            $i = 0;
            foreach ($websites as $website) {
                if (in_array($website->getId(), $data['website'])) {
                    $i++;
                }
            }

            if ($i != $sites) {
                $response = new Response();
                return $response->setStatusCode(404);
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

                if ($website == null) {
                    $response = new Response();
                    return $response->setStatusCode(404);
                }

                foreach ($users as $user) {
                    $em->remove($user);
                }

                $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
                $usersNumber = count($data['user']);
                $i = 0;
                foreach ($users as $user) {
                    if (in_array($user->getUsername(), $data['user'])) {
                        $i++;
                    }
                }

                if ($i != $usersNumber) {
                    $response = new Response();
                    return $response->setStatusCode(404);
                }


                foreach ($data['user'] as $name) {
                    $user = $this->getDoctrine()->getRepository('AppBundle:Users')
                        ->findOneBy(array('username' => $name));

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

