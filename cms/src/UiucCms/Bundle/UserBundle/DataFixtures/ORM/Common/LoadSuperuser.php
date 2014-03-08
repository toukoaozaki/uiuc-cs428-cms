<?php
namespace UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSuperuser implements FixtureInterface, ContainerAwareInterface
{
    const USERNAME = 'admin';
    const EMAIL = 'admin@domain.com';
    const PASSWORD = 'root';
    const FIRST_NAME = 'First name';
    const LAST_NAME = 'Last name';

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

        $userManager = $this->container->get('fos_user.user_manager');

        // Create a new user
        $user = $userManager->createUser();
        $user->setUsername(self::USERNAME);
        $user->setEmail(self::EMAIL);
        $user->setPlainPassword(self::PASSWORD);
        $user->setFirstName(self::FIRST_NAME);
        $user->setLastName(self::LAST_NAME);
        $user->setEnabled(true);
        $user->setSuperAdmin(true);

        $manager->persist($user);
        $manager->flush();
    }
}
?>
