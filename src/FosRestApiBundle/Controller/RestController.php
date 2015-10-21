<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/21/15
 * Time: 4:47 PM
 */
namespace FosRestApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AppBundle\Entity\Users;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestController extends Controller
{
    /**
     * @param string $id
     * @Rest\View
     * @return array()
     */
    public function getAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->find($id);
        if (!$user instanceof Users) {
            throw new NotFoundHttpException('User not found');
        }

        return array('user' => $user);
    }

    /**
     * @Rest\View
     */
    public function allAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')
            ->findAll();
        return array('users' => $users);
    }
}