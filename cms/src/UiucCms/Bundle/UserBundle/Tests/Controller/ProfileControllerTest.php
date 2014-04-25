<?php

namespace UiucCms\Bundle\UserBundle\Tests\Controller;

use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class ProfileControllerTest extends FunctionalTestCase
{
    private $client;
    private $router;
    private $login_url;
    private $profile_url;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
        $this->login_url = $this->router->generate(
            'fos_user_security_login',
            array(),
            true);
        $this->profile_url = $this->router->generate('fos_user_profile_show');
        $this->profile_edit_url = $this->router->generate(
            'fos_user_profile_edit',
            array(),
            true);
    }

    public function testProfilePageProtected()
    {
        $this->client->request('GET', $this->profile_url);
        // the page must be protected with login
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertEquals($this->login_url,
            $this->client->getRequest()->getUri());
    }

    public function testProfilePage()
    {
        static::authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->profile_url);
        // must succeed
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        // must include user profile and links to edit page
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.LoadTestUser::TEST_USERNAME.'")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.LoadTestUser::TEST_EMAIL.'")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.LoadTestUser::TEST_FIRST_NAME.'")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.LoadTestUser::TEST_LAST_NAME.'")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('a[href="'.$this->profile_edit_url.'"]')->count()
        );
    }
}
