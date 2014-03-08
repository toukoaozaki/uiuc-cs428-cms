<?php

namespace UiucCms\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'UiucCmsUserBundle:Default:index.html.twig',
            array('user' => $this->getUser())
        );
    }
}
