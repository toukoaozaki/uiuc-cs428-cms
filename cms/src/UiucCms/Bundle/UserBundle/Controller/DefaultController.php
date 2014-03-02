<?php

namespace UiucCms\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UiucCmsUserBundle:Default:index.html.twig');
    }

    public function profileAction()
	  {
        $user['name'] = 'Eunsoo Roh';
        $user['company'] = 'CS 429';
        $user['phone'] = '123456789';
        $user['email'] = 'roh7@illinois.edu';

        return $this->render(
            'UiucCmsUserBundle:Default:profile.html.twig',
            array('user' => $user)
        );
    }
}
