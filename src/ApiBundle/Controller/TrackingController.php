<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 3:08 PM
 */
namespace ApiBundle\Controller;

use AppBundle\Form\UsersType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Repository\UsersRepository;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class TrackingController extends Controller
{

    /**
     * @Route("pingdom", name = "pingdom")
     * @return array
     */
    public function checkAction()
    {
        $curl = curl_init();
        // Set target URL
        curl_setopt($curl, CURLOPT_URL, "https://api.pingdom.com/api/2.0/checks");
        // Set the desired HTTP method (GET is default, see the documentation for each request)
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        // Set user (email) and password
        curl_setopt($curl, CURLOPT_USERPWD, "bashmach@gmail.com:ww2UBhfZ");
        // Add a http header containing the application key (see the Authentication section of this document)
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("App-Key: fg29h3mmnbbtqrhcgcxp00ra8sgrc02v"));
        // Ask cURL to return the result as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


        // Execute the request and decode the json result into an associative array
        $response = json_decode(curl_exec($curl), true);
        $checks = $response['checks'];

        // Check for errors returned by the API
        if (isset($response['error'])) {
            return [
                'error' => $response['error']['errormessage']
                ];
        }

        // Fetch the list of checks from the response
        return $checks;
    }

    public function turnDownAction()
    {
        return new Response('site down');
    }
}