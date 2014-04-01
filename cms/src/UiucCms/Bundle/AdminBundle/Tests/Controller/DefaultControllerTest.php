<?php

namespace UiucCms\Bundle\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class DefaultControllerTest extends WebTestCase
{

	private $client;
    private $router;
	
	protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->setupFixtures($this->container);
        $this->router = $this->container->get('router');
	
	}
    public function testIndex()
    {

        $crawler = $this->client->request('GET', '/hello/Fabien');

        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
		
	/*
		test that super admin will not be displayed in the list of users that can be promoted
	*/
	public function testShowSuper()
	{
        $crawler = $this->client->request('GET', '/user/admin/show');

        $this->assertTrue($crawler->filter('html:contains("admin@domain.com")')->count() == 0);	
	}
	
	/* 
		test that admin will not be displayed 
	*/
	public function testPromote()
	{
        $crawler = $this->client->request('GET', '/user/admin/show');
		$proCount = $crawler->filter('html:contains("Promote")')->count();
		$link = $crawler->filter('a:contains("Promote")')->eq(0)->link();
		$crawler = $this->client->click($link);
        $this->assertTrue($crawler->filter('html:contains("Promote")')->count() == $proCount - 1);	
	}
	
    /*
        test that demoted admin will be displayed in other admin
    */
    public function testDemote()
    {
        $crawler = $this->client->request('GET', '/user/admin/show');
		$proCount = $crawler->filter('html:contains("Demote")')->count();
		$link = $crawler->filter('a:contains("Demote")')->eq(0)->link();
		$crawler = $this->client->click($link);
        $this->assertTrue($crawler->filter('html:contains("Demote")')->count() == $proCount - 1);
    }
    
    /*
        test that deleted user will not be displayed
    */
    public function testRemove()
    {
        $crawler = $this->client->request('GET', '/user/admin/show');
		$proCount = $crawler->filter('html:contains("Remove")')->count();
		$link = $crawler->filter('a:contains("Remove")')->eq(0)->link();
		$crawler = $this->client->click($link);
        $this->assertTrue($crawler->filter('html:contains("Remove")')->count() == $proCount - 1);
    }
    
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
}
