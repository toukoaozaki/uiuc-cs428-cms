<?php

namespace UiucCms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UiucCmsAdminBundle:Default:index.html.twig', array('name' => $name));
    }
	
	public function promoteAction()
	{
		$users = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->findAll();
		if(!$users) {
			throw $this->createNotFoundException('No users found.');
		}
		else {
			return $this->render('UiucCmsAdminBundle:Default:index.html.twig', array('users' => $users, ));
		}
	}
}
