<?php

namespace UiucCms\Bundle\PaymentBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use UiucCms\Bundle\PaymentBundle\DataFixtures\ORM\Test\LoadTestOrder;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class PaymentControllerTest extends FunctionalTestCase
{
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
    }

    protected static function getDataFixtures()
    {
        $list = parent::getDataFixtures();
        $list[] = new LoadTestOrder();
        return $list;
    }

    public function testChoosePaymentMethodGet()
    {
        $crawler = $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => LoadTestOrder::TEST_ORDER_NUMBER)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        // must show payment amount and currency
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_AMOUNT.'")'
            )->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_CURRENCY.'")'
            )->count()
        );
    }

    public function testChoosePaymentMethodPost()
    {
        $this->client->request(
            'POST',
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

    public function testChoosePaymentMethodUnauthorized()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $order = $em->find(
            'UiucCms\Bundle\PaymentBundle\Entity\Order',
            LoadTestOrder::TEST_ORDER_NUMBER
        );
        $order->setOwner($this->getUser(self::getUserUsername()));
        $em->persist($order);
        // access should be restricted to the owner of the order, if exists
        $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => LoadTestOrder::TEST_ORDER_NUMBER)
            )
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    public function testPaymentSuccess()
    {
        $crawler = $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => LoadTestOrder::TEST_ORDER_NUMBER)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        // use "Success" method
        $form = $crawler->selectButton('form.choose_payment.submit')->form();
        $form['jms_choose_payment_method[method]']->select('dummy_success');
        $this->client->submit($form);
        // must be a redirection
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        // the result page should be a success
        $this->assertEquals(
            0,
            $crawler->filter('html:contains("failed")')->count()
        );
        // the result page must have order number, amount, currency
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_NUMBER.'")'
            )->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_AMOUNT.'")'
            )->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_CURRENCY.'")'
            )->count()
        );
    }

    public function testPaymentFailure()
    {
        $crawler = $this->client->request(
            'GET',
            $this->router->generate(
                'uiuc_cms_payment_start',
                array('order' => LoadTestOrder::TEST_ORDER_NUMBER)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        // use "Success" method
        $form = $crawler->selectButton('form.choose_payment.submit')->form();
        $form['jms_choose_payment_method[method]']->select('dummy_failure');
        $this->client->submit($form);
        // must be a redirection
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        // the result page should be a failure
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Failed")')->count()
        );
        // the result page must have order number, amount, currency
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_NUMBER.'")'
            )->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_AMOUNT.'")'
            )->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("'.LoadTestOrder::TEST_ORDER_CURRENCY.'")'
            )->count()
        );
    }

    private function getUser($username)
    {
        $manager = $this->container->get('fos_user.user_manager');
        return $manager->findUserByUsername($username);
    }
}
