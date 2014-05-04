<?php

namespace UiucCms\Bundle\AdminBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common\LoadSuperuser;
use UiucCms\Bundle\ConferenceBundle\DataFixtures\ORM\Test\LoadConference;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use UiucCms\Bundle\AdminBundle\Entity\Mail;

use UiucCms\Bundle\AdminBundle\Controller\DefaultController;

class DefaultControllerTest extends FunctionalTestCase
{

    private $client;
    private $router;
    
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');   
    }

    protected static function getDataFixtures()
    {
        $list = parent::getDataFixtures();
        $list[] = new LoadConference();
        return $list;
    }

    /*
        test that super admin will not be displayed in the list of users that can be promoted
    */
    public function testShowSuper()
    {
        $crawler = $this->client->request('GET', '/user/admin/show');

        $this->assertTrue($crawler->filter('html:contains("admin@domain.com")')->count() == 0); 
    }
    
    //test mail can't send because nothing is filled out
    public function testMailFail()
    {
        $this->authenticateSuperuser($this->client);
    
        $crawler = 
            $this->client->request('GET', '/admin/mail/1');
            
        $buttonNode = $crawler->selectButton('Send');
        $form = $buttonNode->form();
        $crawler = $this->client->submit($form);
        
        $count = $crawler->filter('html:contains("Success")')->count();
        
        $this->assertTrue($count > 0);
    }
    
	//test mail doesn't send because body is not filled out
    public function testMailFail2()
    {
        $this->authenticateSuperuser($this->client);
    
        $crawler = 
            $this->client->request('GET', '/admin/mail/1');
            
        $buttonNode = $crawler->selectButton('Send');
        $form = $buttonNode->form();
        
        $form['form[subject]'] = "Test Sub";
        
        $crawler = $this->client->submit($form);
        
        $count = $crawler->filter('html:contains("Success")')->count();
        
        $this->assertTrue($count > 0);
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
        $this->authenticateSuperuser($this->client);
        $crawler = 
            $this->client->request('GET', '/conf/manage/1');
    
        $this->assertTrue($crawler->filter('html:contains("Send mass email")')->count() == 0);  

    }
    
    /* test that email data is properly 
     * passed from the form
     */
    public function testSendMail()
    {
        $this->authenticateSuperuser($this->client);
    
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

	/* checks that mail is successfully sent to target user
	*/
    public function testMailSent()
    {
        $TEST_SUBJ = "Test Sub";
        $TEST_BODY = "Test Body";

        $this->authenticateSuperuser($this->client);
        $this->client->enableProfiler();

        $crawler = $this->client->request('GET', '/admin/mail/1');
            
        $buttonNode = $crawler->selectButton('Send');
        $form = $buttonNode->form();
        
        $form['form[subject]'] = $TEST_SUBJ;
        $form['form[body]'] = $TEST_BODY;
        
        $this->client->enableProfiler();        
        $crawler = $this->client->submit($form); 
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // Check that an e-mail was sent
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting e-mail data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals($TEST_SUBJ, $message->getSubject());
        $this->assertEquals($TEST_BODY, $message->getBody()); 
    }

    /* 
        test that admin will not be displayed 
    */
    public function testPromote()
    {
		$this->statusHelper("Promote");
		
    }

    
    /*
        test that deleted user will not be displayed
    */
    public function testRemove()
    {
		$this->statusHelper("Remove");

    }
	/*
		Helper method for testing status changes
	*/
	protected function statusHelper($command)
	{
		$this->authenticateSuperuser($this->client);

        $crawler = $this->client->request('GET', '/user/admin/show');
        $proCount = $crawler->filter('html:contains("'. $command .'")')->count();
        $link = $crawler->filter('a:contains("'. $command .'")')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertTrue($crawler->filter('html:contains("'. $command .'")')->count() == $proCount - 1);
	
	}
}
