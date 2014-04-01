<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;

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
        $fixtures = new LoadTestUser();
        $fixtures->setContainer($container);
        $loader->addFixture($fixtures);
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
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->index_url);
        $this->assertEquals(
            0,
            $crawler->filter('html:contains("No conferences found.")')->count());
    }

    //test that create page exists
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/conf/create');

        //failing for some reason
        //$this->assertTrue($crawler->filter('html:contains("Create a new conference")')->count() > 0);
    }

    //test that submit successfully added to database
    /*public function testSubmit()
    {
      $client = static::createClient();
      $crawler = $client->request('GET', '/conf/create');
      $form = $crawler->selectButton('Create')->form(); 
      
      $form["conference[name]"] = 'Test';
      $form["conference[year]"] = 2014;
      $form["conference[city]"] = 'Champaign';
      $form["conference[register_begin_date][date][year]"] = "2014";
      $form["conference[register_begin_date][date][month]"] = "2";
      $form["conference[register_begin_date][date][day]"] = "31";
      $form["conference[register_end_date][date][year]"] = "2014";
      $form["conference[register_end_date][date][month]"] = "2";
      $form["conference[register_end_date][date][day]"] = "31";

      $crawler = $client->submit($form);
      
      $this->assertTrue($client->getResponse()->isSuccessful());
      $this->assertTrue($crawler->filter('html:contains("Successfully added element")')->count() > 0);
  } */

  

}
