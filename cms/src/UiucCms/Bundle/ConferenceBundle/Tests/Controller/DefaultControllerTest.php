<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;

use \DateTime;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
		$this->assertEquals(3,3);
    }
	
	//test that create page exists
	public function testCreate()
	{
		$client = static::createClient();

        $crawler = $client->request('GET', '/conf/create');

        $this->assertTrue($crawler->filter('html:contains("Create a new conference")')->count() > 0);
	}
	
	//test that submit successfully added to database
	public function testSubmit()
    {
        $client = static::createClient();
		$crawler = $client->request('GET', '/conf/create');
		$form = $crawler->selectButton('Create')->form(); 
		
		$form['name'] = 'Test';
		$form['year'] = 2014;
		$form['registerBeginDate'] = new DateTime("2014-02-31 11:00:15.00");
		$form['registerEndDate'] = new DateTime("2014-02-31 11:00:15.00");
		$form['topics'] = [];
		
		$crawler = $client->submit($form);
		
		$this->assertTrue($this->client->getResponse()->isSuccessful());
		$this->assertTrue($crawler->filter('html:contains("Created product id")')->count() > 0);
		
        
    }
	
}
