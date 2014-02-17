<?php

namespace uiuc\RailtecBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$profile['name'] = 'Eunsoo Roh';
		$profile['company'] = 'CS 429';
		$profile['phone'] = '123456789';
		$profile['email'] = 'roh7@illinois.edu';
		
        return $this->render('uiucRailtecBundle:Default:index.html.twig', array('profile' => $profile));
    }
}
