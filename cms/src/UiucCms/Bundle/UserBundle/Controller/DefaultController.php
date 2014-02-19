<?php

namespace UiucCms\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UiucCmsUserBundle:Default:index.html.twig');
    }

    public function loginAction()
    {
        return $this->render('UiucCmsUserBundle:Default:login.html.twig');
 
    }
}
