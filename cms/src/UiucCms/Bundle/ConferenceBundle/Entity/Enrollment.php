<?php

namespace UiucCms\Bundle\ConferenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UiucCms\Bundle\PaymentBundle\Entity\Order;

/**
 * Enrollment
 */
class Enrollment
{
    const FEE_STATUS_UNPAID = 0;
    const FEE_STATUS_PAID = 1;
    const FEE_STATUS_EXEMPT = 2;
    const FEE_STATUS_UNKNOWN = 99;

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
     * @var datetime
     */
    private $enrollmentDate;

    /**
     * @var integer
     */
    private $coverFeeStatus;

    /**
     * @var string
     */
    private $food;
    
    /**
     * @var string
     */
    private $paperAbstract;

    /**
     * @var UiucCms\Bundle\PaymentBundle\Entity\Order
     */
    private $currentOrder;
    
    public function __construct()
    {
    }

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

    public function getEnrollmentDate()
    {
        return $this->enrollmentDate;
    }

    public function setEnrollmentDate(\DateTime $date)
    {
        // enforce Doctrine update by creating new object
        $timestamp = $date->getTimestamp();
        $this->enrollmentDate = new \DateTime("@$timestamp");
        return $this;
    }

    public function updateEnrollmentDate()
    {
        $this->enrollmentDate = new \DateTime(null, new \DateTimeZone('UTC'));
    }

    private static function filterCoverFeeStatus($status)
    {
        switch ($status) {
            case static::FEE_STATUS_UNPAID:
            case static::FEE_STATUS_PAID:
            case static::FEE_STATUS_EXEMPT:
                return $status;
            default:
                return static::FEE_STATUS_UNKNOWN;
        }
    }

    public function setCoverFeeStatus($status)
    {
        $this->coverFeeStatus = static::filterCoverFeeStatus($status);
        return $this;
    }

    public function getCoverFeeStatus()
    {
        return $this->coverFeeStatus;
    }
    
    public function setFood($food)
    {
        $this->food = $food;
        
        return $this;
    }
    
    public function getFood()
    {
        return $this->food;
    }
    
    public function setAbstract($abstract)
    {
        $this->paperAbstract = $abstract;
        
        return $this;
    }
    
    public function getAbstract()
    {
        return $this->paperAbstract;
    }

    public function setCurrentOrder(Order $order = null)
    {
        $this->currentOrder = $order;
        return $this;
    }
    
    public function getCurrentOrder()
    {
        return $this->currentOrder;
    }
}
