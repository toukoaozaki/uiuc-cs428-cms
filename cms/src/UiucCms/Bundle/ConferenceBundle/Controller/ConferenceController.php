<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\Entity\Enrollment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \DateTime;

class ConferenceController extends Controller
{
    public function indexAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findAll();
        if (!$conferences) {
            throw $this->createNotFoundException('No conferences found.');
        }

        else {
            return $this->render(
                'UiucCmsConferenceBundle:Conference:index.html.twig', 
                array('conferences' => $conferences, ));
        }
    }

    public function createAction() 
    {
        $conference = new Conference();

        $form = $this->createForm(
            new ConferenceType(),
            $conference,
            array('action' => $this->generateUrl('uiuc_cms_conference_submit'),)
        );
     
        return $this->render(
            'UiucCmsConferenceBundle:Conference:create.html.twig',
            array( 'form' => $form->createView(),));
    }

    public function submitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();             
        $form = $this->createForm(new ConferenceType(), new Conference());
        $form->handleRequest($request);

        $conference = $form->getData();

        $em->persist($conference);
        $em->flush();
        return new Response(
            'Successfully added element '.$conference->getId().' to database.');
    }

    public function displayAction($id)
    {
        $conference = $this->getDoctrine()
                           ->getRepository('UiucCmsConferenceBundle:Conference')
                           ->find($id);
        
        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        
        // We want to see if the user has already enrolled in this particular 
        // conference.
        
        $userId = $this->getUser()->getId();
        
        $query = $enrollments->createQueryBuilder('e')
                             ->where('e.conferenceId = :confId')
                             ->andWhere('e.attendeeId = :userId')
                             ->setParameters(['userId' => $userId, 'confId' => $id])
                             ->getQuery();

        $enrollment = $query->getOneOrNullResult();
       
        $enrolled = true;

        if ($enrollment == null) {
            $enrolled = false;
        }

        if (!$conference) {
            throw $this->createNotFoundException(
                'No conference found with id: '.$id);
        }

        else {
            return $this->render(
                'UiucCmsConferenceBundle:Conference:display.html.twig', 
                array('name'     => $conference->getName(), 
                      'year'     => $conference->getYear(), 
                      'city'     => $conference->getCity(),
                      'begin'    => $conference->getRegisterBeginDate(),
                      'end'      => $conference->getRegisterEndDate(),
                      'confId'   => $id,
                      'enrolled' => $enrolled)
            );
        }
    }

    public function enrollAction($id)
    {
        $userId = $this->getUser()->getId();
        
        $enrollment = new Enrollment();
        $enrollment->setConferenceId($id);
        $enrollment->setAttendeeId($userId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($enrollment);
        $em->flush();

        return $this->displayAction($id);
    }

    public function enrolledInAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findAll();
       
        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        
        $userId = $this->getUser()->getId();
        
        $enrolledConferences = array();

        foreach ($conferences as $key => $conference) {
            $confId = $conference->getId();
            $query = $enrollments->createQueryBuilder('e')
                             ->where('e.conferenceId = :confId')
                             ->andWhere('e.attendeeId = :userId')
                             ->setParameters(['userId' => $userId, 'confId' => $confId])
                             ->getQuery();

            $enrollment = $query->getOneOrNullResult();

            if ($enrollment != null) {
                array_push($enrolledConferences, $conference);
            }
        }

       

        return $this->render(
            'UiucCmsConferenceBundle:Conference:index.html.twig', 
            array('conferences' => $enrolledConferences, ));

    }

}
