<?php

namespace FosRestApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FosRestApiBundle:Default:index.html.twig', array('name' => $name));
    }
}
