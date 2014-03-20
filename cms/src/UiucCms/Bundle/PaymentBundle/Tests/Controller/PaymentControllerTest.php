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
}
