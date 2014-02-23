<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use Symfony\Component\HttpFoundation\Response;

use \DateTime;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $conference = new Conference();
        $conference->setName('Test Conference');
        $conference->setYear(2014);
        $conference->setCity('Champaign');
        $conference->setRegisterBeginDate(new DateTime("2014-02-31 11:00:15.00"));
        $conference->setRegisterEndDate(new DateTime("2014-02-31 11:00:15.00"));
        $conference->setTopics([]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($conference);
        $em->flush();

        return new Response('Created product id'.$conference->getId());
        // return $this->render('UiucCmsConferenceBundle:Default:index.html.twig', array('name' => $name));
    }

    public function createAction() 
    {
        $conference = new Conference();
        
        $form = $this->createFormBuilder($conference)
                    ->add('name', 'text')
                    ->add('year', 'integer')
                    ->add('city', 'text')
                    ->add('register_begin_date', 'datetime')
                    ->add('register_end_date', 'datetime')
                    ->add('topics', 'collection', array('type' => 'text'))
                    ->add('create', 'submit')
                    ->getForm();
        return $this->render('UiucCmsConferenceBundle:Default:create.html.twig', array( 'form' => $form->createView(),));
    }

}
