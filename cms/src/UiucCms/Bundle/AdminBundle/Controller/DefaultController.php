<?php

namespace UiucCms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UiucCmsAdminBundle:Default:index.html.twig', array('name' => $name));
    }
	
	public function showAction()
	{
		$users = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->findAll();
		if(!$users) {
			throw $this->createNotFoundException('No users found.');
		}
		else {
			return $this->render('UiucCmsAdminBundle:Default:users.html.twig', array('users' => $users, ));
		}
	}
    
    public function promoteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->find($id);
        $user->addRole("ROLE_ADMIN");
        
        $em->persist($user);
        $em->flush();
        
        return showAction();
    }
}
