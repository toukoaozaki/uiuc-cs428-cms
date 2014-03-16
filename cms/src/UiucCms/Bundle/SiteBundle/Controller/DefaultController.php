<?php

namespace UiucCms\Bundle\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UiucCmsSiteBundle:Default:index.html.twig');
    }
}
