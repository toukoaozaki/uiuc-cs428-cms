<?php

namespace UiucCms\Bundle\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/Fabien');

        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
		
	/*
		test that super admin will not be displayed in the list of users that can be promoted
	*/
	public function testShow()
	{
		$client = static::createClient();

        $crawler = $client->request('GET', '/user/admin/show');

        $this->assertTrue($crawler->filter('html:contains("admin@domain.com")')->count() == 0);	
	}
	
	/* 
		test that admin will not be displayed 
	*/
	
	public function testShow()
	{
		$client = static::createClient();

        $crawler = $client->request('GET', '/user/admin/show');

        $this->assertTrue($crawler->filter('html:contains("test@uiuc.edu")')->count() == 0);	
	}
}
