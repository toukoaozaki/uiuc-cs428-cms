<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UiucCmsConferenceBundle:Default:index.html.twig', array('name' => $name));
    }
}
