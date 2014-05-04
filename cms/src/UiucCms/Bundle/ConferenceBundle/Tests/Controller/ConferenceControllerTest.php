<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\DataFixtures\ORM\Test\LoadConference;
use \Exception;

use \DateTime;
use \DateInterval;

class ConferenceControllerTest extends FunctionalTestCase
{
    private $client;
    private $router;
    private $profile_url;
    private $index_url;
    private $create_conf_url;
    private $view_created_conf_url;
    private $test_conf_url;
    private $direct_enroll_url;
    private $enrolled_in_url;

    private $validName = "RailTEC UIUC";
    private $shortName = "Ra";
    private $validYear = "2014";
    private $validCity = "Champaign";
    private $validTopic = "Trains";
    private $validMaxEnrollment = "5";
    private $validCoverFee = "10.99";
    private $invalidYear = "2013";
    private $invalidStartTime;
    private $lateStartTime; 
    private $validStartTime; 
    private $validEndTime;

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
        $this->index_url = $this->router->generate(
            'uiuc_cms_conference_list_not_enrolled',
            array(),
            true);
        $this->create_conf_url = $this->router->generate(
            'uiuc_cms_conference_create',
            array(),
            true);
     
        $this->view_created_conf_url = $this->router->generate(
            'uiuc_cms_conference_view_created',
            array(),
            true);
        $this->test_conf_url = $this->router->generate(
            'uiuc_cms_conference_display',
            array( "id" => 1 ),
            true);
        $this->direct_enroll_url = $this->router->generate(
            'uiuc_cms_conference_enrollInfo',
            array( "id" => 1 ),
            true);
        $this->enrolled_in_url = $this->router->generate(
            'uiuc_cms_conference_list_enrolled',
            array(),
            true);

        $this->invalidStartTime = new DateTime('now');
        $this->lateStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('10 days'));
        $this->validStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('1 days'));
        $this->validEndTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('5 days'));
    }

    protected static function getDataFixtures()
    {
        $list = parent::getDataFixtures();
        $list[] = new LoadConference();
        return $list;
    }

    private function populateDateForm($form, $startDate, $endDate)
    {
        $form['conference[register_begin_date][month]'] = 
            (int) $startDate->format('m');
        $form['conference[register_begin_date][day]'] = 
            (int) $startDate->format('d');
        $form['conference[register_begin_date][year]'] = 
            (int) $startDate->format('Y');
        $form['conference[register_end_date][month]'] = 
            (int) $endDate->format('m');
        $form['conference[register_end_date][day]'] = 
            (int) $endDate->format('d');
        $form['conference[register_end_date][year]'] = 
            (int) $endDate->format('Y');
        return $form;
    }

    public function testIndex()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->index_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Conferences")')->count());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Rails Conference")')->count());
    }

    public function testCreatePermissionsAdmin()
    {
        $this->authenticateSuperuser($this->client);
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Create a new conference:")')->count() > 0);
    }
    
    public function testCreatePermissionsUser()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }

    public function testViewCreatedPermissionsAdmin()
    {
        $this->authenticateSuperuser($this->client);
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Your Conferences")')->count() > 0);
    }
    
    public function testViewCreatedPermissionsUser()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }

    public function testSuccessfulValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $form = $this->populateDateForm(
            $form, 
            $this->validStartTime, 
            $this->validEndTime);
    
        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect(); 

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Details for")')->count());

    }

    public function testNoNameValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("enter a name")')->count());

    }
  
    public function testShortNameValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->shortName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("minimum length 3")')->count());

    }

    public function testNoYearValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("enter a year")')->count());

    }


    public function testNoTopicValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("least one topic")')->count());

    }

    
    public function testValidStartDate()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;
   
        $form = $this->populateDateForm(
            $form, 
            $this->invalidStartTime, 
            $this->validEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("date in the future")')->count());

    }

    public function testLateStartDate()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $form = $this->populateDateForm(
            $form, 
            $this->lateStartTime, 
            $this->validEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("after the start")')->count());

    }

    public function testMismatchingYear()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->invalidYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;
 
        $form = $this->populateDateForm(
            $form, 
            $this->validStartTime, 
            $this->validEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Year must be")')->count());

    }
    
    public function testRegistrationClosed()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->test_conf_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Registration has closed")')->count());
    }

    public function testDirectRegistrationClosed()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->test_conf_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Registration has closed")')->count());
    }

    public function testEnrolledInConferences()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->enrolled_in_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("You are not enrolled")')->count());
    }

}
