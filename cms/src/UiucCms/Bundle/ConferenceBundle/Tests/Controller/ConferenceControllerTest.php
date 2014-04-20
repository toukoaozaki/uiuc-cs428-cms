<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use \Exception;

use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common\LoadSuperuser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


use \DateTime;
use \DateInterval;

class DefaultControllerTest extends WebTestCase
{
    private $client;
    private $router;
    private $login_url;
    private $profile_url;
    private $index_url;
    private $create_conf_url;
    private $view_created_conf_url;

    private $validName = "RailTEC UIUC";
    private $shortName = "Ra";
    private $validYear = "2014";
    private $validCity = "Champaign";
    private $validTopic = "Trains";
    private $invalidStartTime;
    private $lateStartTime; 
    private $validStartTime; 
    private $validEndTime;

    private function setupFixtures($container)
    {
        // get entity manager
        $em = $container->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        // purge fixtures
        $executor->purge();
        // load fixtures
        $loader = new Loader();
        $userFixtures = new LoadTestUser();
        $userFixtures->setContainer($container);
        $adminFixtures = new LoadSuperuser();
        $adminFixtures->setContainer($container);
        $loader->addFixture($userFixtures);
        $loader->addFixture($adminFixtures);
        $executor->execute($loader->getFixtures());
        
        $this->invalidStartTime = new DateTime('now');
        $this->lateStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('10 days'));
        $this->validStartTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('1 days'));
        $this->validEndTime = (new DateTime('now'))->
            add(DateInterval::createFromDateString('5 days'));

    }

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->setupFixtures($this->container);
        $this->router = $this->container->get('router');
        $this->index_url = $this->router->generate(
            'uiuc_cms_conference_homepage',
            array(),
            true);
        $this->login_url = $this->router->generate(
            'fos_user_security_login',
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
      
    }

    
    private function authenticate($type)
    {
        $crawler = $this->client->request('GET', $this->login_url);
        $buttonNode = $crawler->selectButton('Login');
        $form = $buttonNode->form();

        if ($type == 'user') {
            $form['_username'] = LoadTestUser::TEST_USERNAME;
            $form['_password'] = LoadTestUser::TEST_PASSWORD;
        }
        else if ($type == 'admin') {
            $form['_username'] = LoadSuperuser::USERNAME;
            $form['_password'] = LoadSuperuser::PASSWORD;
        }
        else {
            throw new Exception('Invalid authenticate() parameter.');
        }
        
        $this->client->submit($form);
    }

    public function testIndex()
    {
        $this->authenticate('user');
        $crawler = $this->client->request('GET', $this->index_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Conferences")')->count());
    }

    public function testCreatePermissionsAdmin()
    {
        $this->authenticate('admin');
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Create a new conference:")')->count() > 0);
    }
    
    public function testCreatePermissionsUser()
    {
        $this->authenticate('user');
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }

    public function testViewCreatedPermissionsAdmin()
    {
        $this->authenticate('admin');
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Your Conferences")')->count() > 0);
    }
    
    public function testViewCreatedPermissionsUser()
    {
        $this->authenticate('user');
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }
    public function testSuccessfulValidator()
    {
        $this->authenticate('admin'); 
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

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Details for")')->count());

    }

    public function testNoNameValidator()
    {
        $this->authenticate('admin'); 
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
        $this->authenticate('admin'); 
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
        $this->authenticate('admin'); 
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
        $this->authenticate('admin'); 
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
        $this->authenticate('admin'); 
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
        $this->authenticate('admin'); 
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
}
