<?php

namespace UiucCms\Bundle\TestUtilityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Doctrine data fixtures used for testing.
 */
abstract class FixtureBase implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public final function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of allowed environments.
     *
     * Override this method to restrict fixture loading to specific environment.
     * Return value of null or empty array is equivalent to no restrictions.
     */
    protected function getAllowedEnvironments()
    {
        return array('test');
    }

    /**
     * Actually loads the fixture using the object manager.
     */
    protected abstract function doLoad(ObjectManager $manager);

    /**
     * {@inheritDoc}
     */
    public final function load(ObjectManager $manager)
    {
        $env = $this->container->get('kernel')->getEnvironment();
        $allowed = $this->getAllowedEnvironments();
        if (!$allowed || in_array($env, $allowed)) {
            $this->doLoad($manager);
        }
    }
}
