<?php

namespace UiucCms\Bundle\PaymentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
    }

    public function testPaymentCaptureForbidden()
    {
        $crawler = $this->client->request(
            'GET',
            $this->router->generate('uiuc_cms_payment_capture')
        );

        $code = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            $this->client->getResponse()->isForbidden(),
            "$code is not 403 Forbidden");
    }
}
