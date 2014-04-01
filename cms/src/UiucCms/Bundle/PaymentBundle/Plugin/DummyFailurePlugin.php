<?php

namespace UiucCms\Bundle\PaymentBundle\Plugin;

use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\FinancialException;


/**
 * Dummy failure plugin for testing purposes.
 */
class DummyFailurePlugin extends AbstractPlugin
{
    public function approve(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function approveAndDeposit(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function deposit(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function credit(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function reverseApproval(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function reverseDeposit(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function reverseCredit(FinancialTransactionInterface $transaction, $retry = false)
    {
        $this->process($transaction);
    }

    public function processes($paymentSystemName)
    {
        return 'dummy_failure' === $paymentSystemName;
    }

    public function isIndependentCreditSupported()
    {
        return false;
    }

    private function process(FinancialTransactionInterface $transaction)
    {
        // force the transaction to fail
        $transaction->setResponseCode('Failed');
        $transaction->setReasonCode('DummyPaymentActionFailed');
        throw new FinancialException('DummyPaymentAction failed.');
    }
}
