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
    const TEST_RETURN_URL = 'https://illinois.edu';

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
        if (!in_array(
            $this->container->get('kernel')->getEnvironment(),
            array('test')
        )) {
            // skip in non-test environment
            return;
        }

        // Create a new order for testing
        $order = new Order(self::TEST_ORDER_CURRENCY, self::TEST_ORDER_AMOUNT);
        $order->setOrderNumber(self::TEST_ORDER_NUMBER);
        $order->setReturnUrl(self::TEST_RETURN_URL);
        // override id generator, so TEST_ORDER_NUMBER goes through
        $metadata = $manager->getClassMetaData(get_class($order));
        $metadata->setIdGeneratorType(
            \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE
        );
        $manager->persist($order);
        $manager->flush();
    }
}
?>
