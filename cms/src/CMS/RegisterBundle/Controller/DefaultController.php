<?php

namespace CMS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CMSRegisterBundle:Default:index.html.twig', array('name' => $name));
    }
}
