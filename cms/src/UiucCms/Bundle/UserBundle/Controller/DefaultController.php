<?php

namespace UiucCms\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $admin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        return $this->render(
            'UiucCmsUserBundle:Default:index.html.twig', 
            array('admin' => $admin,
            'user'  => $this->getUser()));
    }

    public function profileAction()
    {
        return $this->render(
            'UiucCmsUserBundle:Default:index.html.twig',
            array('user' => $this->getUser())
        );
    }
}
