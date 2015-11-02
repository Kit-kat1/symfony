<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RoutingController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showDashboardAction()
    {
        $checks = $this->get('app.pingdom_connect')->connect();

        $websites = count($checks);
        $up = 0;
        $down = 0;
        foreach ($checks as $check) {
            if ($check['status'] == 'up') {
                $up++;
            } elseif ($check['status'] == 'down') {
                $down++;
            }
        }
//        $track['up'] = $up;
//        $track['down'] = $down;
//        $track['websites'] = $websites;
//        $status = json_encode($track);
//        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser(), 'track' => $status));
        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'up' => $up, 'down' => $down));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function showAdminAction()
    {
        //Gives access only for user with role SUPER ADMIN
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $users = $this->getDoctrine()->getEntityManager()
            ->createQuery("SELECT u FROM AppBundle:Users u WHERE u.username != 'admin' ")
            ->getResult();
        return $this->render('admin2/admin.html.twig', array('users' => $users, 'user' => $this->getUser()));
    }

    /**
     * @Route("/dashboard/websites", name="websites")
     */
    public function allWebsitesAction()
    {
        $web = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        $checks = $this->get('app.pingdom_connect')->connect();

        $webStatus = [];
        foreach ($checks as $check) {
            $webStatus[] = array($check['hostname'] => $check['status']);
        }

        $url = [];
        foreach ($checks as $check) {
            $url[] = $check['hostname'];
        }


        $webUrl = [];
        foreach ($web as $website) {
            $webUrl[] = $website->getUrl();
        }

        $diff = array_diff($webUrl, $url);
        $cross = array_uintersect($url, $webUrl, "strcasecmp");

        foreach ($diff as $url) {
            $site = $this->getDoctrine()->getRepository('AppBundle:Websites')
                ->findOneBy(array('url' => $url));
            $site->setStatus();
            $em = $this->getDoctrine()->getManager();
            $em->remove($site);
            $em->flush();
        }

        foreach ($webStatus as $siteStatus) {
            $url = array_keys($siteStatus);
            if (in_array($url[0], $cross)) {
                $site = $this->getDoctrine()->getRepository('AppBundle:Websites')
                    ->findOneBy(array('url' => $url[0]));
                $status = array_values($siteStatus);
                $site->setStatus($status[0]);
                $em = $this->getDoctrine()->getManager();
                $em->persist($site);
                $em->flush();
            }
        }
        return $this->render('admin2/websites.html.twig', array('websites' => $web));
    }
}