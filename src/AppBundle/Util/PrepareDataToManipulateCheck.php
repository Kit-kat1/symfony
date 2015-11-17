<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/12/15
 * Time: 12:50 PM
 */
namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PrepareDataToManipulateCheck
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $data
     * @param $id
     * @return string
     */
    public function getBody($data, $id = null)
    {
        $name = trim($data['name']);
        $name = str_replace(' ', '+', $name);
        $type = 'http';
        $host = trim($data['url']);

        if ($id == null) {
            $body = "name=" . $name . "&type=" . $type . "&host=" . $host;
        } else {
            $body = "name=" . $name . "&host=" . $host;
        }
        return $body;
    }

    /**
     * @param $website
     * @return int
     */
    public function getCheckId($website)
    {
        $checks = $this->container->get('app.pingdom_get_checks')->getChecks();

        $url = $website->getUrl();
        $checkId = 0;
        foreach ($checks['checks'] as $check) {
            if ($check['hostname'] == $url) {
                $checkId = $check['id'];
            }
        }
        return $checkId;
    }
}