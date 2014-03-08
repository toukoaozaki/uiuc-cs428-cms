<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;

use Symfony\Component\HttpFoundation\Request;
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

        // These three lines add objects to the database.
        $em = $this->getDoctrine()->getManager();
        $em->persist($conference); 
        $em->flush();

        return new Response('Created product id'.$conference->getId());
    }

    public function createAction() 
    {
        $conference = new Conference();

        $form = $this->createForm(new ConferenceType(), $conference, array('action' => $this->generateUrl('uiuc_cms_conference_submit'),));
     
        return $this->render('UiucCmsConferenceBundle:Default:create.html.twig', array( 'form' => $form->createView(),));
    }

    public function submitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();             
        $form = $this->createForm(new ConferenceType(), new Conference());
        $form->handleRequest($request);

        $conference = $form->getData();

        $em->persist($conference);
        $em->flush();
        return new Response('Successfully added element '.$conference->getId().' to database.');
    }

    public function displayAction($id)
    {
        $conference = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->find($id);
        if (!$conference) {
            throw $this->createNotFoundException('No conference found with id: '.$id);
        }

        else {
            return $this->render('UiucCmsConferenceBundle:Default:display.html.twig', array('name' => $conference->getName(), 
                                                                                            'year' => $conference->getYear(), 
                                                                                            'city' => $conference->getCity()));
        }
    }

}
