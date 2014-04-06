<?php

namespace UiucCms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\AdminBundle\Entity\Mail;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UiucCmsAdminBundle:Default:index.html.twig', array('name' => $name));
    }
    
    private function getUserEmail()
    {
        $userId = $this->getUser()->getId();
        
        $userRepo = $this->getDoctrine()
                        ->getRepository('UiucCmsUserBundle:User');
                        
        $user = $userRepo->find($userId);
        
        return $user->getEmail();
    }
    
    private function getAttendeeEmails($confId)
    {       
        //setup repos
        $enrollmentRepo = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
                            
        $enrollments = $enrollmentRepo->findBy(
                            array('conferenceId' => $confId));
                            
        $userRepo = $this->getDoctrine()
                        ->getRepository('UiucCmsUserBundle:User');
        
        //accumulator
        $userEmails = array();
        
        //loop through enrollment. for each enrollment,
        //if conference_id = $confId, add it to accumulator
        
        foreach ($enrollments as $enrollment)
        {
            $attendeeId = $enrollment->getAttendeeId();
            $user = $userRepo->find($attendeeId);
            
            $userEmail = $user->getEmail();
            $userEmails[] = $userEmail;
        }
        
        return $userEmails;
    }
    
    public function mailAction($id, Request $request)
    {
        $mail = new Mail();
        
        $fromEmail = $this->getUserEmail();
        $toEmails = $this->getAttendeeEmails($id);
        
        //fill in real data here later
        $mail->setTo($toEmails);
        $mail->setFrom($fromEmail);
        
        $form = $this->createFormBuilder($mail)
            ->add('subject', 'text')
            ->add('body', 'textarea')
            ->add('send', 'submit')
            ->getForm();
            
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            //mailing stuffs here
            
            return $this->render('UiucCmsAdminBundle:Default:index.html.twig', array('name' => 'name'));
        }
        
        return $this->render('UiucCmsAdminBundle:Default:mail.html.twig', array('form' => $form->createView()));
    }
	
	public function showAction()
	{
        $admin = $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN');
		$users = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->findAll();
		if(!$users) {
			throw $this->createNotFoundException('No users found.');
		}
		else {
			return $this->render('UiucCmsAdminBundle:Default:users.html.twig', array('admin' => $admin, 'users' => $users));
		}
	}
    
    public function promoteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->find($id);
        $user->addRole("ROLE_ADMIN");
        
        $em->persist($user);
        $em->flush();
        
        return $this->redirect($this->generateUrl('uiuc_cms_promote_user'));;
    }
    
    public function demoteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->find($id);
        if($user->hasRole("ROLE_ADMIN")) {
            $user->removeRole("ROLE_ADMIN");
        
            $em->persist($user);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('uiuc_cms_promote_user'));;
    }
    
    public function removeAction($id)
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('UiucCmsUserBundle:User')->find($id);
        
        $em->remove($user);
        $em->flush();
        
        return $this->redirect($this->generateUrl('uiuc_cms_promote_user'));;
    }
}
