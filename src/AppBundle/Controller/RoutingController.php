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
        $curl = curl_init();
        // Set target URL
        curl_setopt($curl, CURLOPT_URL, $this->getParameter('curlopt_url'));
        // Set the desired HTTP method (GET is default, see the documentation for each request)
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        // Set user (email) and password
        curl_setopt($curl, CURLOPT_USERPWD, $this->getParameter('userpwd'));
        // Add a http header containing the application key (see the Authentication section of this document)
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("App-Key: " . $this->getParameter('app_key')));
        // Ask cURL to return the result as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // Execute the request and decode the json result into an associative array
        $response = json_decode(curl_exec($curl), true);
//        $response = $this->get('app.pingdom_connect')->connect();
        $checks = $response['checks'];

        // Check for errors returned by the API
        if (isset($response['error'])) {
            return [
                'error' => $response['error']['errormessage']
            ];
        }
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
        $track['up'] = $up;
        $track['down'] = $down;
        $track['websites'] = $websites;
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
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return $this->render('admin2/admin.html.twig', array('users' => $users, 'user' => $this->getUser()));
    }
}