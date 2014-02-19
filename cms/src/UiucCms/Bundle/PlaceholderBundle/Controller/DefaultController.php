<?php

namespace UiucCms\Bundle\PlaceholderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UiucCmsPlaceholderBundle:Default:index.html.twig');
    }

    public function helloAction($name)
    {
        return $this->render('UiucCmsPlaceholderBundle:Default:hello.html.twig', array('name' => $name));
    }
}
