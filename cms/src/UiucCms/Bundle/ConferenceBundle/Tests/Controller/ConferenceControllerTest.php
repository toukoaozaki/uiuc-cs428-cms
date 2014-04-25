<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use \Exception;

use \DateTime;
use \DateInterval;

class DefaultControllerTest extends FunctionalTestCase
{
    private $client;
    private $router;
    private $profile_url;
    private $index_url;
    private $create_conf_url;
    private $view_created_conf_url;

    private $validName = "RailTEC UIUC";
    private $shortName = "Ra";
    private $validYear = "2014";
    private $validCity = "Champaign";
    private $validTopic = "Trains";
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
            'uiuc_cms_conference_homepage',
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
      
        $this->invalidStartTime = new DateTime('now');
        $this->lateStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('10 days'));
        $this->validStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('1 days'));
        $this->validEndTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('5 days'));
    }

    public function testIndex()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->index_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Conferences")')->count());
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
        $form['conference[register_begin_date][date][month]'] = 
            (int) $this->validStartTime->format('m');
        $form['conference[register_begin_date][date][day]'] = 
            (int) $this->validStartTime->format('d');
        $form['conference[register_begin_date][date][year]'] = 
            (int) $this->validStartTime->format('Y');
        $form['conference[register_begin_date][time][hour]'] = 
            (int) $this->validStartTime->format('H');
        $form['conference[register_begin_date][time][minute]'] = 
            (int) $this->validStartTime->format('i');
        $form['conference[register_end_date][date][month]'] = 
            (int) $this->validEndTime->format('m');
        $form['conference[register_end_date][date][day]'] = 
            (int) $this->validEndTime->format('d');
        $form['conference[register_end_date][date][year]'] = 
            (int) $this->validEndTime->format('Y');
        $form['conference[register_end_date][time][hour]'] = 
            (int) $this->validEndTime->format('H');
        $form['conference[register_end_date][time][minute]'] = 
            (int) $this->validEndTime->format('i');

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
    
        $form['conference[register_begin_date][date][month]'] = 
            (int) $this->invalidStartTime->format('m');
        $form['conference[register_begin_date][date][day]'] = 
            (int) $this->invalidStartTime->format('d');
        $form['conference[register_begin_date][date][year]'] = 
            (int) $this->invalidStartTime->format('Y');
        $form['conference[register_begin_date][time][hour]'] = 
            (int) $this->invalidStartTime->format('H');
        $form['conference[register_begin_date][time][minute]'] = 
            (int) $this->invalidStartTime->format('i');
        $form['conference[register_end_date][date][month]'] = 
            (int) $this->validEndTime->format('m');
        $form['conference[register_end_date][date][day]'] = 
            (int) $this->validEndTime->format('d');
        $form['conference[register_end_date][date][year]'] = 
            (int) $this->validEndTime->format('Y');
        $form['conference[register_end_date][time][hour]'] = 
            (int) $this->validEndTime->format('H');
        $form['conference[register_end_date][time][minute]'] = 
            (int) $this->validEndTime->format('i');
         

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

        $form['conference[register_begin_date][date][month]'] = 
            (int) $this->lateStartTime->format('m');
        $form['conference[register_begin_date][date][day]'] = 
            (int) $this->lateStartTime->format('d');
        $form['conference[register_begin_date][date][year]'] = 
            (int) $this->lateStartTime->format('Y');
        $form['conference[register_begin_date][time][hour]'] = 
            (int) $this->lateStartTime->format('H');
        $form['conference[register_begin_date][time][minute]'] = 
            (int) $this->lateStartTime->format('i');
        $form['conference[register_end_date][date][month]'] = 
            (int) $this->validEndTime->format('m');
        $form['conference[register_end_date][date][day]'] = 
            (int) $this->validEndTime->format('d');
        $form['conference[register_end_date][date][year]'] = 
            (int) $this->validEndTime->format('Y');
        $form['conference[register_end_date][time][hour]'] = 
            (int) $this->validEndTime->format('H');
        $form['conference[register_end_date][time][minute]'] = 
            (int) $this->validEndTime->format('i');
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
        $form['conference[register_begin_date][date][month]'] = 
            (int) $this->validStartTime->format('m');
        $form['conference[register_begin_date][date][day]'] = 
            (int) $this->validStartTime->format('d');
        $form['conference[register_begin_date][date][year]'] = 
            (int) $this->validStartTime->format('Y');
        $form['conference[register_begin_date][time][hour]'] = 
            (int) $this->validStartTime->format('H');
        $form['conference[register_begin_date][time][minute]'] = 
            (int) $this->validStartTime->format('i');
        $form['conference[register_end_date][date][month]'] = 
            (int) $this->validEndTime->format('m');
        $form['conference[register_end_date][date][day]'] = 
            (int) $this->validEndTime->format('d');
        $form['conference[register_end_date][date][year]'] = 
            (int) $this->validEndTime->format('Y');
        $form['conference[register_end_date][time][hour]'] = 
            (int) $this->validEndTime->format('H');
        $form['conference[register_end_date][time][minute]'] = 
            (int) $this->validEndTime->format('i');

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Year must be")')->count());

    }
}
