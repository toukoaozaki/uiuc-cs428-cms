<?php
namespace UiucCms\Bundle\ConferenceBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use UiucCms\Bundle\TestUtilityBundle\DataFixtures\ORM\FixtureBase;

use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\Entity\Enrollment;

class LoadConference extends FixtureBase
{
    protected function doLoad(ObjectManager $manager)
    {
        $conference = new Conference();
        
        $conference->setName("Rails Conference");
        $conference->setYear(2014);
        $conference->setCity("Chicago");
        $conference->setRegisterBeginDate(new \DateTime("2012-07-08 11:14:15.638276"));
        $conference->setRegisterEndDate(new \DateTime("2012-07-09 11:14:15.638277"));
        $conference->setTopics("HCI");
        $conference->setCreatedBy(2);
        $conference->setMaxEnrollment(5);
        $conference->setCoverFee(119.50);

        $manager->persist($conference);
        $manager->flush();
    }
}
?>
