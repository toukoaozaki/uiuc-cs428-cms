<?php

namespace UiucCms\Bundle\PaymentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\PaymentBundle\DataFixtures\ORM\Test\LoadTestOrder;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class PaymentControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
        $this->setupFixtures($this->container);
    }

    public function testChoosePaymentMethodGet()
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => LoadTestOrder::TEST_ORDER_NUMBER)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testChoosePaymentMethodInvalidOrder()
    {
        // access without specifying valid order should not be allowed
        $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => -1)
            )
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
        $this->client->request(
            'POST',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => -1)
            )
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    public function testPaymentSuccess()
    {
    }

    public function testPaymentFailure()
    {
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
        $fixtures = new LoadTestOrder();
        $fixtures->setContainer($container);
        $loader->addFixture($fixtures);
        $executor->execute($loader->getFixtures());
    }
}
