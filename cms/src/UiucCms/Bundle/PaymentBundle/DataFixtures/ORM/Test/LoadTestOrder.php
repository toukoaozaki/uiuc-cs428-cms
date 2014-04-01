<?php
namespace UiucCms\Bundle\PaymentBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UiucCms\Bundle\PaymentBundle\Entity\Order;

class LoadTestOrder implements FixtureInterface, ContainerAwareInterface
{
    const TEST_ORDER_NUMBER = 666;
    const TEST_ORDER_AMOUNT = 163.84;
    const TEST_ORDER_CURRENCY = 'USD';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Create a new order for testing
        $order = new Order(self::TEST_ORDER_CURRENCY, self::TEST_ORDER_AMOUNT);
        $order->setOrderNumber(self::TEST_ORDER_NUMBER);
        $manager->persist($order);
        $manager->flush();
    }
}
?>
