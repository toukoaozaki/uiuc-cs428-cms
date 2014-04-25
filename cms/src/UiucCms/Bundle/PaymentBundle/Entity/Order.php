<?php

namespace UiucCms\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use UiucCms\Bundle\UserBundle\Entity\User;

/**
 * Order in financial transaction. Could refer to material goods or fees
 * e.g. cover fee.
 *
 * @ORM\Entity
 * @ORM\Table(name="cms_order")
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
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $originalAmount;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank()
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="UiucCms\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $returnUrl;

    /**
     * Construct an order.
     */
    public function __construct($currency, $amount, $originalAmount = null)
    {
        $this->currency = $currency;
        $this->amount = $amount;
        $this->originalAmount = 
            ($originalAmount === null) ? $amount : $originalAmount;
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
     * Set 3-letter currency type
     *
     * @param string $currency
     * @return Order
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get 3-letter currency type
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
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

    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;
        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;
        return $this;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }
}
