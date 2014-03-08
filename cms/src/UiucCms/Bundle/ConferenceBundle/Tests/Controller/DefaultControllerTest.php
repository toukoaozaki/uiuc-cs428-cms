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
        $conference = new Conference();
        $conference->setName('Test Conference');
        $conference->setYear(2014);
        $conference->setCity('Champaign');
        $conference->setRegisterBeginDate(new DateTime("2014-02-31 11:00:15.00"));
        $conference->setRegisterEndDate(new DateTime("2014-02-31 11:00:15.00"));
        $conference->setTopics([]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($conference); 
        $em->flush();
		
		$id = $conference->getId();
		$newConference = $this->getDoctrine()
                            ->getRepository('UiucCmsConferenceBundle:Conference')
                            ->find($id);
        if (!$newConference) {
            $this->assertEquals(2,3);
        }

        else {
            $this->assertEquals('Test Conference', $newConference->getName());
			$this->assertEquals(2014, $newConference->getYear());
			$this->assertEquals('Champaign', $newConference->getCity());
			
        }

		$em->remove($newConference);
		$em->flush();
        
    }
	
}
