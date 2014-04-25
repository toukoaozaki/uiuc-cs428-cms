<?php

namespace UiucCms\Bundle\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $login_url = $router->generate('fos_user_security_login');
        $crawler = $client->request('GET', $login_url);
        // the page must be accessible
        $this->assertTrue($client->getResponse()->isSuccessful());
        // login page must have username and password fields
        $this->assertEquals(
            1,
            $crawler->filter('input[name=_username]')->count()
        );
        $this->assertEquals(
            1,
            $crawler->filter('input[name=_password]')->count()
        );
    }

    public function testLoginPageRegisterLink()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $login_url = $router->generate('fos_user_security_login');
        $crawler = $client->request('GET', $login_url);
        // the page must be accessible
        $this->assertTrue($client->getResponse()->isSuccessful());
        $link = $crawler->selectLink('form.security.login.register_account');
        // login page must have register link
        $this->assertNotNull($link);
        $this->assertEquals(
            $router->generate('fos_user_registration_register', array(), true),
            $link->link()->getUri()
        );
    }

    public function testLoginPagePasswordResetLink()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $login_url = $router->generate('fos_user_security_login');
        $crawler = $client->request('GET', $login_url);
        // the page must be accessible
        $this->assertTrue($client->getResponse()->isSuccessful());
        $link = $crawler->selectLink('form.security.login.reset_password');
        // login page must have register link
        $this->assertNotNull($link);
        $this->assertEquals(
            $router->generate('fos_user_resetting_request', array(), true),
            $link->link()->getUri()
        );
    }
}
