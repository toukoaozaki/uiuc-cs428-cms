<?php
namespace UiucCms\Bundle\UserBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use UiucCms\Bundle\TestUtilityBundle\DataFixtures\ORM\FixtureBase;

class LoadTestUser extends FixtureBase
{
    const TEST_USERNAME = 'test';
    const TEST_EMAIL = 'test@uiuc.edu';
    const TEST_PASSWORD = 'unsecure';
    const TEST_FIRST_NAME = 'John';
    const TEST_LAST_NAME = 'Doe';


    protected function doLoad(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        // Create a new user
        $user = $userManager->createUser();
        $user->setUsername(self::TEST_USERNAME);
        $user->setEmail(self::TEST_EMAIL);
        $user->setPlainPassword(self::TEST_PASSWORD);
        $user->setFirstName(self::TEST_FIRST_NAME);
        $user->setLastName(self::TEST_LAST_NAME);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }
}
