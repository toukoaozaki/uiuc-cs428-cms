<?php

namespace UiucCms\Bundle\PaymentBundle\Plugin;

use JMS\Payment\CoreBundle\Plugin\SuccessfulTransactionPlugin;

/**
 * Dummy plugin for testing purposes.
 */
class DummySuccessPlugin extends SuccessfulTransactionPlugin
{
    public function processes($paymentSystemName)
    {
        return 'dummy_success' === $paymentSystemName;
    }
}
