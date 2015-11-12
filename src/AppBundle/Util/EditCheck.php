<?php

namespace AppBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Executes some manipulations on the users
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class EditCheck
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $body
     * @param $id
     * @return mixed
     */
    public function update($id, $body)
    {
        $curl = curl_init();
        // Set target URL
        curl_setopt($curl, CURLOPT_URL, $this->container->getParameter('pingdom.checks_url') . "/" .$id);
        // Set the desired HTTP method (GET is default, see the documentation for each request)
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        // Set user (email) and password
        curl_setopt($curl, CURLOPT_USERPWD, $this->container->getParameter('pingdom.mail_pwd'));
        // Add a http header containing the application key (see the Authentication section of this document)
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("App-Key: " . $this->container->getParameter('pingdom.app_key')));
        // Ask cURL to return the result as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POST, 1);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        // Execute the request and decode the json result into an associative array
        $response = json_decode(curl_exec($curl), true);

        return $response;
    }
}