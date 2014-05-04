<?php
namespace UiucCms\Bundle\TestUtilityBundle\TestFixtures;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test\LoadTestUser;
use UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common\LoadSuperuser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

abstract class FunctionalTestCase extends WebTestCase
{
    private static $login_url = null;

    protected static function createClient(
        array $options = array(),
        array $server = array()
    ) {
        $client = parent::createClient();
        self::setupDataFixtures();
        self::setupRoutes();
        return $client;
    }

    // setup routes used internally within FunctionalTestCase
    private static function setupRoutes()
    {
        $container = self::$kernel->getContainer();
        $router = $container->get('router');
        self::$login_url = $router->generate('fos_user_security_login');
    }

    /**
     * Override this function to add/override specific data fixtures to load.
     */
    protected static function getDataFixtures()
    {
        return array(
            new LoadTestUser(),
            new LoadSuperUser(),
        );
    }

    protected static final function setupDataFixtures()
    {
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        // purge fixtures
        $executor->purge();
        // load fixtures
        $loader = new Loader();
        
        foreach (static::getDataFixtures() as $f) {
            $f->setContainer($container);
            $loader->addFixture($f);
        }
        $executor->execute($loader->getFixtures());
    }

    protected static function authenticate($client, $username, $password)
    {
        $crawler = $client->request('GET', self::$login_url);
        $buttonNode = $crawler->selectButton('security.login.submit');
        $form = $buttonNode->form();

        $form['_username'] = $username;
        $form['_password'] = $password;
        
        $client->submit($form);
    }

    protected static function authenticateUser($client)
    {
        self::authenticate(
            $client,
            LoadTestUser::TEST_USERNAME,
            LoadTestUser::TEST_PASSWORD
        );
    }

    protected static function authenticateSuperuser($client)
    {
        self::authenticate(
            $client,
            LoadSuperuser::USERNAME,
            LoadSuperuser::PASSWORD
        );
    }

    protected static function getUserUsername()
    {
        return LoadTestUser::TEST_USERNAME;
    }

    protected static function getSuperuserUsername()
    {
        return LoadSuperuser::USERNAME;
    }
}
