<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\Entity\Enrollment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use \DateTime;

class ConferenceController extends Controller
{
    /**
     * Queries the database for all conferences and returns a page listing them.
     */
    public function indexAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findAll();
        return $this->render(
            'UiucCmsConferenceBundle:Conference:index.html.twig', 
            array('conferences' => $conferences, ));
    }

    /**
     * Generates a form with the necessary fields to create a conference.
     */
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
    
    /**
     * Validates the form's contents and submits the request to the database.
     *
     * If the validator fails, kicks the user back to the create conference page
     * with an error telling them to fill out all forms.
     */
    public function submitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();             
        $form = $this->createForm(new ConferenceType(), new Conference());
        $form->handleRequest($request);

        $conference = $form->getData();

        // TODO: get built in validator to work for whole objects.

        $nameNotBlank = new NotBlank();
        $minLength3 = new Length(array('min' => 3));
        $yearNotBlank = new NotBlank();
        $cityNotBlank = new NotBlank();
        $topicsNotBlank = new notBlank();

        // These forms also need highlighting eventually.
        $nameNotBlank->message = 'Please fill out a name.';
        $minLength3->minMessage = 'Please enter a name of minimum length 3.';
        $yearNotBlank->message = 'Please fill out a year.';
        $cityNotBlank->message = 'Please fill out a city.';
        $topicsNotBlank->message = 'Please add at least one topic.';
        
        // TODO: validate topic format

        $validator = $this->get('validator');

        $errorList = array( 
            $validator->validateValue($conference->getName(), $nameNotBlank),
            $validator->validateValue($conference->getName(), $minLength3),
            $validator->validateValue($conference->getYear(), $yearNotBlank),
            $validator->validateValue($conference->getCity(), $cityNotBlank),
            $validator->validateValue($conference->getTopics(), $topicsNotBlank)
                          );
       
        // TODO: list all errors instead of just the first one that appears
        foreach ($errorList as $error) {
            if (count($error) != 0) {
                return $this->render(
                    'UiucCmsConferenceBundle:Conference:create.html.twig',
                    array( 'form'  => $form->createView(),
                           'errors' => $error));
            }
        }

        $conference->setCreatedBy($this->getUser()->getId());

        $em->persist($conference);
        $em->flush();
        return $this->manageAction($conference->getId());
    }

    /**
     * Displays a particular conference and all of its fields. Also checks whether or
     * not a user has already enrolled in that particular conference.
     */
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
                array('conference' => $conference,
                      'enrolled'   => $enrolled));
        }
    }

    /**
     * Submits an enrollment for a conference.
     */
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

    /**
     * Queries the database for all conferences that a user is currently enrolled in.
     */
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
        
        // This could probably use its own page one day
        return $this->render(
            'UiucCmsConferenceBundle:Conference:index.html.twig', 
            array('conferences' => $enrolledConferences, ));
    }

    /**
     * Shows all of the conferences that an admin has created.
     */
    public function viewCreatedAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findByCreatedBy($this->getUser()->getId());
        
        return $this->render(
            'UiucCmsConferenceBundle:Conference:view_created.html.twig',
            array('conferences' => $conferences, ));
    }

    /**
     * Allows an admin to view a particular conference and all of its attendees.
     */
    public function manageAction($id)
    {
        $conference = $this->getDoctrine()
                           ->getRepository('UiucCmsConferenceBundle:Conference')
                           ->find($id);
        
        $users = $this->getDoctrine()
                      ->getRepository('UiucCmsUserBundle:User');

        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment')
                            ->findByConferenceId($id);
        
        $attendees = array();
        
        foreach ($enrollments as $enrollment) {
            $attendee = $users->find($enrollment->getAttendeeId());
            array_push($attendees, $attendee);
        }

        return $this->render('UiucCmsConferenceBundle:Conference:manage.html.twig',
            array('conference' => $conference, 
                  'attendees'  => $attendees));
    }
    
}
