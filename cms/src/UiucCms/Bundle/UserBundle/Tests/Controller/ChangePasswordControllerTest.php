<?php
namespace UiucCms\Bundle\UserBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;

class ChangePasswordControllerTest extends FunctionalTestCase
{
    const NEW_PASSWORD = '123456';

    private $client;
    private $router;
    private $request_url;
    private $send_email_url_abs;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->router = $this->client->getContainer()->get('router');
        $this->request_url = $this->router->generate(
            'fos_user_resetting_request'
        );
        $this->send_email_url_abs = $this->router->generate(
            'fos_user_resetting_send_email',
            array(),
            true
        );
    }

    public function testPasswordResetPage()
    {
        // check whether the page has minimum required ui elements
        $crawler = $this->client->request('GET', $this->request_url);
        // username/email box
        $this->assertEquals(
            1,
            $crawler->filter('input[name=username]')->count()
        );
        // submit button
        $this->assertNotNull(
            $crawler->selectButton('change_password.submit')
        );
    }

    public function testPasswordResetNonexistentUser()
    {
        $crawler = $this->submitPasswordResetRequest('ThisUserCannotExist.com');
        // check handler url
        $this->assertEquals(
            $this->send_email_url_abs,
            $this->client->getHistory()->current()->getUri()
        );
        // check presence of error message
        $this->assertEquals(
            1,
            $crawler->filter(
                'html:contains("resetting.request.invalid_username")'
            )->count()
        );
    }

    public function testPasswordResetSendmail()
    {
        $crawler = $this->submitPasswordResetRequest(static::getUserUsername());
        // check handler url
        $this->assertEquals(
            $this->send_email_url_abs,
            $this->client->getHistory()->current()->getUri()
        );
        // email should have been sent
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $message = $mailCollector->getMessages()[0];
        // must be using the right translation
        $this->assertContains('resetting.email.message', $message->getBody());
    }

    public function testPasswordResetCapture()
    {
        $this->testPasswordResetSendmail();
        $resetUrl = $this->getResetUrl(static::getUserUsername());
        $crawler = $this->client->request('GET', $resetUrl);
        // must land on the right page with the form
        $form = $crawler->selectButton('resetting.reset.submit')->form();
        $this->assertNotNull($form);
        // try initiating actual reset
        $form['fos_user_resetting_form[new][first]'] = self::NEW_PASSWORD;
        $form['fos_user_resetting_form[new][second]'] = self::NEW_PASSWORD;
        $crawler = $this->client->submit($form);
        // try authenticating the user with the new password
        self::authenticate(
            $this->client,
            self::getUserUsername(),
            self::NEW_PASSWORD
        );
    }

    private function getResetUrl($username)
    {
        $manager = $this->client->getContainer()->get('fos_user.user_manager');
        $user = $manager->findUserByUsername($username);
        $token = $user->getConfirmationToken();
        return $this->router->generate(
            'fos_user_resetting_reset',
            array('token' => $token)
        );
    }

    private function submitPasswordResetRequest($username)
    {
        $crawler = $this->client->request('GET', $this->request_url);
        $form = $crawler->selectButton('resetting.request.submit')->form();
        $form['username'] = $username;
        $this->client->enableProfiler();
        return $this->client->submit($form);
    }

}
