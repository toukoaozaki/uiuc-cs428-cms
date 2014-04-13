<?php
namespace UiucCms\Bundle\UserBundle\DataFixtures\ORM\Common;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\Entity\Enrollment;

class LoadConference implements FixtureInterface, ContainerAwareInterface
{

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
        $conference = new Conference();
        
        $conference->setName("Rails Conference");
        $conference->setYear(2014);
        $conference->setCity("Chicago");
        $conference->setRegisterBeginDate(new \DateTime("2012-07-08 11:14:15.638276"));
        $conference->setRegisterEndDate(new \DateTime("2012-07-09 11:14:15.638277"));
        $conference->setTopics("HCI");
        $conference->setCreatedBy(2);
        

        $manager->persist($conference);
        $manager->flush();
    }
}
?>
