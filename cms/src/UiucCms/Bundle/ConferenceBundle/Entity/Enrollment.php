<?php

namespace UiucCms\Bundle\ConferenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enrollment
 */
class Enrollment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $conferenceId;

    /**
     * @var integer
     */
    private $attendeeId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     * @return Enrollment
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return integer 
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Set attendeeId
     *
     * @param integer $attendeeId
     * @return Enrollment
     */
    public function setAttendeeId($attendeeId)
    {
        $this->attendeeId = $attendeeId;

        return $this;
    }

    /**
     * Get attendeeId
     *
     * @return integer 
     */
    public function getAttendeeId()
    {
        return $this->attendeeId;
    }
}
