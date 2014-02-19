<?php

namespace UiucCms\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UiucCmsUserBundle:Default:index.html.twig', array('name' => $name));
    }

    public function loginAction()
    {
        return $this->render('UiucCmsUserBundle:Default:login.html.twig');
 
    }
}
