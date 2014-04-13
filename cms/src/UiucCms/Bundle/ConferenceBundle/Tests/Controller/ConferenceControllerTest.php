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

class DefaultControllerTest extends WebTestCase
{
    private $client;
    private $router;
    private $login_url;
    private $profile_url;
    private $index_url;
    private $create_conf_url;

    private $validName = "RailTEC UIUC";
    private $shortName = "Ra";
    private $validYear = "2014";
    private $validCity = "Champaign";
    private $validTopic = "Trains";

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
            $crawler->filter('html:contains("Conferences:")')->count());
    }

    public function testCreateAdmin()
    {
        $this->authenticate('admin');
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Create a new conference:")')->count() > 0);
    }
    
    public function testCreateUser()
    {
        $this->authenticate('user');
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertFalse(
            $crawler->filter(
                'html:contains("Create a new conference:")')->count() > 0);
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
            $crawler->filter('html:contains("Please fill out a name")')->count());

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
            $crawler->filter('html:contains("Please fill out a year")')->count());

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
            $crawler->filter('html:contains("Please add at least one topic")')
                ->count());

    }

}
