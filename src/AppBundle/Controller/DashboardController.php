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
        $checks = $this->get('app.pingdom_get_checks')->getChecks();
        $websites = count($checks['checks']);

        $down = $this->get('app.pingdom_websites_status')->sitesDown($checks);
        $up = $this->get('app.pingdom_websites_status')->sitesDown($checks);

        return $this->render('admin2/dashboard.html.twig', array('user' => $this->getUser(), 'websites' => $websites,
            'up' => $up, 'down' => $down));
    }
}