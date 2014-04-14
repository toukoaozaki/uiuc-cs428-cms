<?php

namespace UiucCms\Bundle\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common\LoadSuperuser;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common\LoadConference;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use UiucCms\Bundle\AdminBundle\Entity\Mail;

use UiucCms\Bundle\AdminBundle\Controller\DefaultController;

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

	private function authenticate($type)
    {
        $crawler = $this->client->request('GET', $this->router->generate(
            'fos_user_security_login',
            array(),
            true));
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
	
    /* test mail object
     *
     */
    public function testMailObject()
    {
        $mail = new Mail();
        
        $to = array();
        $to[] = "test@test.com";
        
        $from = "from@from.com";
        $subject = "subject";
        $body = "body";
        
        $mail->setTo($to);
        $mail->setFrom($from);
        $mail->setSubject($subject);
        $mail->setBody($body);
        
        $this->assertTrue($mail->getSubject() == $subject);
        $this->assertTrue($mail->getBody() == $body);
    }
    
    /* test that "send mass email" is only
     * displayed if there are attendees
     */
    public function testNoAttendees()
    {
        $this->authenticate('admin');
        $crawler = 
            $this->client->request('GET', '/conf/manage/1');
    
        //$this->assertTrue($crawler->filter('html:contains("Send mass email")')->count() == 0);	

    }
    
    /* test that email data is properly 
     * passed from the form
     */
    public function testSendMail()
    {
        $this->authenticate('admin');
    
        $crawler = 
            $this->client->request('GET', '/admin/mail/1');
            
        $buttonNode = $crawler->selectButton('Send');
        $form = $buttonNode->form();
        
        $form['form[subject]'] = "Test Sub";
        $form['form[body]'] = "Test Body";
        
        $crawler = $this->client->submit($form);
        
        $count = $crawler->filter('html:contains("Success")')->count();
        
        $this->assertTrue($count > 0);
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
        //$crawler = $this->client->request('GET', '/user/admin/show');
		//$proCount = $crawler->filter('html:contains("Demote")')->count();
		//$link = $crawler->filter('a:contains("Demote")')->eq(0)->link();
		//$crawler = $this->client->click($link);
        //$this->assertTrue($crawler->filter('html:contains("Demote")')->count() == $proCount - 1);
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
        
        $adminFixtures = new LoadSuperuser();
        $adminFixtures->setContainer($container);
        
        $conferenceFixtures = new LoadConference();
        $conferenceFixtures->setContainer($container);

        $loader->addFixture($conferenceFixtures);
        $loader->addFixture($fixtures);
        $loader->addFixture($adminFixtures);

        $executor->execute($loader->getFixtures());
    }
}
