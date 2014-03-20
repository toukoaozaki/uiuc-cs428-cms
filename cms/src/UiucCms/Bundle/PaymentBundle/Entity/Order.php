<?php

namespace UiucCms\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

/**
 * Order in financial transaction. Could refer to material goods or fees
 * e.g. cover fee.
 *
 * @ORM\Entity
 * @ORM\Table(name="order")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $orderNumber;

    /**
     * @ORM\OneToOne(targetEntity="JMS\Payment\CoreBundle\Entity\PaymentInstruction")
     */
    private $paymentInstruction;

    /**
     * @ORM\Column(type="decimal", precision=2)
     */
    private $originalAmount;

    /**
     * @ORM\Column(type="decimal", precision=2)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * Construct an order.
     */
    public function __construct($orderNumber, $amount, $originalAmount = null)
    {
        $this->amount = $amount;
        $this->originalAmount = 
            ($originalAmount === null) ? $amount : $originalAmount;
        $this->orderNumber = $orderNumber;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     * @return Order
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string 
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set originalAmount
     *
     * @param string $originalAmount
     * @return Order
     */
    public function setOriginalAmount($originalAmount)
    {
        $this->originalAmount = $originalAmount;

        return $this;
    }

    /**
     * Get originalAmount
     *
     * @return string 
     */
    public function getOriginalAmount()
    {
        return $this->originalAmount;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return Order
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set paymentInstruction
     *
     * @param PaymentInstruction $paymentInstruction
     * @return Order
     */
    public function setPaymentInstruction(
        PaymentInstruction $paymentInstruction = null
    ) {
        $this->paymentInstruction = $paymentInstruction;

        return $this;
    }

    /**
     * Get paymentInstruction
     *
     * @return PaymentInstruction 
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }
}
