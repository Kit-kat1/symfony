<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showDashboardAction()
    {
        $checks = $this->get('app.pingdom_connect')->connect();
        $checks = $checks['checks'];
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
        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'up' => $up, 'down' => $down));
    }
}