<?php

namespace UiucCms\Bundle\ConferenceBundle\Controller;

use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use UiucCms\Bundle\ConferenceBundle\Form\Type\InfoType;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\Entity\Enrollment;
use UiucCms\Bundle\PaymentBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface as PaymentInstruction;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use \DateTime;

class ConferenceController extends Controller
{
    /**
     * Queries the database for all conferences and returns a page listing them.
     *
     * @Template
     */
    public function listAction()
    {
        $user = $this->getUser();
        $conferences = $this->getDoctrine()
            ->getRepository('UiucCmsConferenceBundle:Conference')
            ->findAll();

        $enrollments = array();
        $enrolled = array();

        // setup defaults
        foreach ($conferences as $conf) {
            $enrollments[$conf->getId()] = 0;
            $enrolled[$conf->getId()] = false;
        }

        // find enrollment information
        $enrollmentRepo = $this->getDoctrine()
            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        $query = $enrollmentRepo->createQueryBuilder('e')
            ->select('e.conferenceId, count(e.attendeeId)')
            ->groupBy('e.conferenceId')
            ->getQuery();
        foreach ($query->getResult() as $row) {
            $enrollments[$row['conferenceId']] = $row[1];
        }
        // find enrollments of current user
        $query = $enrollmentRepo->createQueryBuilder('e')
            ->select('e.conferenceId')
            ->where('e.attendeeId=:userId')
            ->setParameters(['userId' => $user->getId()])
            ->getQuery();
        foreach ($query->getResult() as $row) {
            $enrolled[$row['conferenceId']] = true;
        }

        return array(
            'conferences' => $conferences,
            'enrollments' => $enrollments,
            'enrolled' => $enrolled,
        );
    }

    public function listNotEnrolledAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findAll();
        
        $user = $this->getUser();

        $unenrolledConferences = array();

        foreach ($conferences as $key => $conference) {
            $enrollment = $this->getEnrollment($user, $conference);

            if ($enrollment == null) {
                array_push($unenrolledConferences, $conference);
            }
        }

        return $this->render(
            'UiucCmsConferenceBundle:Conference:listNotEnrolled.html.twig', 
            array('conferences' => $unenrolledConferences, ));
    }

    /**
     * Generates a form with the necessary fields to create a conference.
     *
     * @Template
     */
    public function createAction() 
    {
        $conference = new Conference();

        $form = $this->createForm(
            new ConferenceType(),
            $conference,
            array('action' => $this->generateUrl('uiuc_cms_conference_submit'),)
        );
     
        return array('form' => $form->createView());
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
        $yearNotBlank = new NotBlank();
        $cityNotBlank = new NotBlank();
        $topicNotBlank = new NotBlank();
        $minLength3 = new Length(array('min' => 3));
        $maxNotBlank = new NotBlank();
        $coverNotBlank = new NotBlank();

        $nameNotBlank->message = 'Please enter a name.';
        $yearNotBlank->message = 'Please enter a year.';
        $cityNotBlank->message = 'Please enter a city.';
        $topicNotBlank->message = 'Please enter at least one topic.';
        $minLength3->minMessage = 'Please enter a name of minimum length 3.';
        $maxNotBlank->message = 'Please provide the maximum number of registration slots.';
        $coverNotBlank->message = 'Please provide the cover fee.';
        $invalidYear = 'Year must be after registration opening';
        $invalidStartDate = 'Please select a date in the future.';
        $invalidEndDate = 'Please select an end date after the start date';

        $validator = $this->get('validator');

        $errorList = array( 
            $validator->validateValue($conference->getName(), $nameNotBlank),
            $validator->validateValue($conference->getName(), $minLength3),
            $validator->validateValue($conference->getYear(), $yearNotBlank),
            $validator->validateValue($conference->getCity(), $cityNotBlank),
            $validator->validateValue($conference->getTopics(), $topicNotBlank),
            $validator->validateValue($conference->getMaxEnrollment(), $maxNotBlank),
            $validator->validateValue($conference->getCoverFee(), $coverNotBlank),
                          );
       

        foreach ($errorList as $error) {
            if (count($error) != 0) {
                return $this->render(
                    'UiucCmsConferenceBundle:Conference:create.html.twig',
                    array( 'form'  => $form->createView(),
                           'error' => $error[0]->getMessage()));
            }
        }

    
        // Check that the conference is set to take place in the future. 
        // Also see if the end date > start date.
        if ($conference->getRegisterBeginDate()->format('U') < date('U')) {
                return $this->render(
                    'UiucCmsConferenceBundle:Conference:create.html.twig',
                    array( 'form'  => $form->createView(),
                           'error' => $invalidStartDate));
        }

        if ($conference->getRegisterBeginDate()->format('U') > 
            $conference->getRegisterEndDate()->format('U')) {
                return $this->render(
                    'UiucCmsConferenceBundle:Conference:create.html.twig',
                    array( 'form'  => $form->createView(),
                           'error' => $invalidEndDate));
        }

        if ((int)$conference->getYear() < 
            $conference->getRegisterBeginDate()->format('Y')) {
                return $this->render(
                    'UiucCmsConferenceBundle:Conference:create.html.twig',
                    array( 'form'  => $form->createView(),
                           'error' => $invalidYear));
        }
        // set the maintainer
        $conference->setCreatedBy($this->getUser()->getId());

        $em->persist($conference);
        $em->flush();
   
        return $this->redirect(
            $this->generateUrl(
                'uiuc_cms_conference_manage',
                array('id' => $conference->getId())
            )
        );
    }

    /**
     * Displays a particular conference and all of its fields. Also checks whether or
     * not a user has already enrolled in that particular conference. Additionally,
     * checks whether or not registration is full.
     */
    public function displayAction(Conference $conference)
    {
        if (!$conference) {
            throw $this->createNotFoundException(
                'No conference found with id: '.$conference->getId()
            );
        }
        
        // We must check whether or not registration is full.
        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        $query = $enrollments->createQueryBuilder('e')
                             ->select('count(e.attendeeId)')
                             ->where('e.conferenceId = :confId')
                             ->setParameters(['confId' => $conference->getId()])
                             ->getQuery();
        $currentEnrollments = $query->getResult();
        $isFull = ($currentEnrollments >= $conference->getMaxEnrollment()); 
        
        // We want to see if the user has already enrolled in this particular 
        // conference.
        $user = $this->getUser();
        $enrollment = $this->getEnrollment($user, $conference);
       
        return $this->render(
            'UiucCmsConferenceBundle:Conference:display.html.twig',
            array(
                'conference' => $conference,
                'enrollment' => $enrollment,
                'isFull' => $isFull
            )
        );
    }

    public function payFeesAction(Conference $conference)
    {
        if (!$conference) {
            throw $this->createNotFoundException(
                'No conference found with id: '.$conference->getId()
            );
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $user = $this->getUser();
        $enrollment = $this->getEnrollment($user, $conference);
        if (!$enrollment) {
            throw $this->createNotFoundException(
                'User is not enrolled to the conference.'
            );
        }

        // check payment status
        switch ($enrollment->getCoverFeeStatus()) {
            case Enrollment::FEE_STATUS_UNPAID:
                break;
            case Enrollment::FEE_STATUS_PAID:
            case Enrollment::FEE_STATUS_EXEMPT:
                // user has already paid. redirect them to the conference
                return $this->redirect(
                    $this->generateUrl(
                        'uiuc_cms_conference_display',
                        array('id' => $conference->getId())
                    )
                );
            default:
                throw $this->createNotFoundException(
                    'Unknown payment status'
                );
        }
        // fee is not paid yet.
        $order = $enrollment->getCurrentOrder();
        if (!$order) {
            // no order was placed yet. create a new order
            $order = new Order('USD', $conference->getCoverFee());
            $order->setOwner($user);
            $order->setReturnUrl(
                $this->generateUrl(
                    'uiuc_cms_conference_pay_fee',
                    array('id' => $conference->getId()),
                    true
                )
            );
            $enrollment->setCurrentOrder($order);
            $em->persist($order);
            $em->persist($enrollment);
        }
        // see whether the order is complete
        $instr = $order->getPaymentInstruction();
        if ($instr && $instr->getState() == PaymentInstruction::STATE_CLOSED) {
            if ($instr->getAmount() == $instr->getDepositedAmount()) {
                // user has already paid. redirect them to the conference
                $enrollment->setCoverFeeStatus(Enrollment::FEE_STATUS_PAID);
                $enrollment->setCurrentOrder(null);
                $em->persist($enrollment);
            } else {
                // payment instruction is closed. remove it from the order
                $order->setPaymentInstruction(null);
                $em->persist($order);
            }
            $em->flush();
            return $this->redirect(
                $this->generateUrl(
                    'uiuc_cms_conference_display',
                    array('id' => $conference->getId())
                )
            );
        }
        $em->flush();
        return $this->forward(
            'uiuc_cms.payment.controller:choosePaymentAction',
            array('order' => $order->getOrderNumber())
        );
    }

    /**
     * Submits an enrollment for a conference. First checks to see if registration
     * is still open. Also checks to see if there is still room for registration.
     */
    public function enrollAction(Request $request, Conference $conference)
    {
        if ($conference->getRegisterEndDate()->format('U') < date('U')) {
            return $this->redirect(
                $this->generateUrl(
                    'uiuc_cms_conference_display',
                    array('id' => $conference->getId())
                )
            );
        }

        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        $query = $enrollments->createQueryBuilder('e')
                             ->where('e.conferenceId = :confId')
                             ->setParameters(['confId' => $conference->getId()])
                             ->getQuery();
        $currentEnrollments = count($query->getResult());
        if ($currentEnrollments >= $conference->getMaxEnrollment()) 
        {
            return $this->redirect(
                $this->generateUrl(
                    'uiuc_cms_conference_display',
                    array('id' => $conference->getId())
                )
            );
        }

        $user = $this->getUser();
        $userId = $user->getId();
        $confId = $conference->getId();
        
        $form = $this->createForm(new InfoType());
        $form->handleRequest($request);
        $food = $form['food']->getData();
        $abstract = $form['abstract']->getData();
        if (null === $this->getEnrollment($user, $conference)) {
            
            $enrollment = new Enrollment();
            $enrollment->setConferenceId($confId);
            $enrollment->setAttendeeId($userId);
            $enrollment->updateEnrollmentDate();
            $enrollment->setCoverFeeStatus(Enrollment::FEE_STATUS_UNPAID);
            $enrollment->setFood($food);
            $enrollment->setAbstract($abstract);

            $em = $this->getDoctrine()->getManager();
            $em->persist($enrollment);
            $em->flush();
        }

        return $this->redirect(
            $this->generateUrl(
                'uiuc_cms_conference_display',
                array('id' => $confId)
            )
        );
    }

    private function getEnrollment($user, $conference)
    {
        $enrollments = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Enrollment');
        
        $confId = $conference->getId();
        $userId = $user->getId();
        $query = $enrollments->createQueryBuilder('e')
                         ->where('e.conferenceId = :confId')
                         ->andWhere('e.attendeeId = :userId')
                         ->setParameters(['userId' => $userId, 'confId' => $confId])
                         ->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * Queries the database for all conferences that a user is currently enrolled in.
     */
    public function listEnrolledAction()
    {
        $conferences = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->findAll();
       
        $user = $this->getUser();
        
        $enrolledConferences = array();

        foreach ($conferences as $key => $conference) {
            $enrollment = $this->getEnrollment($user, $conference);

            if ($enrollment != null) {
                array_push($enrolledConferences, $conference);
            }
        }
        
        return $this->render(
            'UiucCmsConferenceBundle:Conference:listEnrolled.html.twig', 
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
            'UiucCmsConferenceBundle:Conference:viewCreated.html.twig',
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
    
    /**
     * Generates a form with the necessary fields for enrollment information.
     */
    public function enrollInfoAction(Conference $conference) 
    {
        if ($conference->getRegisterEndDate()->format('U') < date('U')) {
            return $this->render(
                'UiucCmsConferenceBundle:Conference:display.html.twig',
                array(
                    'conference' => $conference,
                    'error' => "Registration has closed."
                )
            );
        }

        $confId = $conference->getId();
        $form = $this->createForm(
            new InfoType(),
            null,
            array(
                'action' => $this->generateUrl(
                    'uiuc_cms_conference_enroll',
                    array('id' => $confId)
                )
            )
        );
     
        return $this->render(
            'UiucCmsConferenceBundle:Conference:info.html.twig',
            array( 'form' => $form->createView(),));
    }

    /**
     * Be able to view enrolled people's abstracts.
     */
    public function viewEnrolledAbstractAction($confId, $attendId)
    {
        
        $attendee = $this->getDoctrine()
                          ->getRepository('UiucCmsUserBundle:User')
                          ->find($attendId);

        $conference = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->find($confId);

        $enrollment = $this->getEnrollment($attendee, $conference);


        return $this->render(
            'UiucCmsConferenceBundle:Conference:view_abstract.html.twig', 
            array('enrollment' => $enrollment, 
                  'attendee' => $attendee));

    }


}
