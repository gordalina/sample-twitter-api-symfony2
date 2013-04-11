<?php

namespace Twitter\DomainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TwitterDomainBundle:Default:index.html.twig', array('name' => $name));
    }
}
