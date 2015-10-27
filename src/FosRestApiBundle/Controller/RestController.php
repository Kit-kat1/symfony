<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/21/15
 * Time: 4:47 PM
 */
namespace FosRestApiBundle\Controller;

use AppBundle\Form\WebsitesType;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Users;
use AppBundle\Entity\Websites;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FormEvent;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\DeserializationContext;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UsersType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestController extends FOSRestController
{
//    /**
//     * @param string $id
//     * @Rest\View
//     * @return array()
//     */
//    public function getAction($id)
//    {
//        $user = $this->getDoctrine()->getRepository('AppBundle:Users')
//            ->find($id);
//        if (!$user instanceof Users) {
//            throw new NotFoundHttpException('User not found');
//        }
////        $serializer = SerializerBuilder::create()->build();
////        $serializer->serialize($user, 'json');
//
////        var_dump($user);die();
//        return array('user' => $user);
//    }

    /**
     * Gets the thread for a given id.
     *
     * @param string $id
     *
     * @return View
     */
    public function getAction($id)
    {
        $website = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->find($id);

        if (null === $website) {
            throw new NotFoundHttpException(sprintf("Thread with id '%s' could not be found.", $id));
        }

        return array('website' => $website);
    }


    /**
     * @Rest\View
     */
    public function allAction()
    {
        $websites = $this->getDoctrine()->getRepository('AppBundle:Websites')
            ->findAll();
        return array('websites' => $websites);
    }

    /**
     * @Rest\View
     * @param  Request $request
     * @return array
     */
    public function newAction(Request $request)
    {
        $website = new Websites();

        $form = $this->createForm(new WebsitesType(), $website);
        $data = $request->request->all();

        $children = $form->all();

        $toBind = array_intersect_key($data, $children);
        $form->submit($toBind);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();


            return $this->handleView($this->view($website));
        }
        return $this->handleView($this->view($form, 400));
    }

    /**
     * @Rest\View
     * @param  Request $request
     * @return array()
     */
    public function editAction(Request $request, Websites $website)
    {
        $form = $this->createForm(new WebsitesType(), $website);
        $data = $request->request->all();

        $children = $form->all();

        $toBind = array_intersect_key($data, $children);
        $form->submit($toBind);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();

            return $this->handleView($this->view(null, 204));
        }
        return $this->handleView($this->view($form, 400));
    }

    /**
     * @Rest\View(statusCode=204)
     */
    public function removeAction(Websites $website)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($website);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}