<?php

namespace UiucCms\Bundle\PaymentBundle\Plugin;

use JMS\Payment\CoreBundle\Plugin\SuccessfulTransactionPlugin;

/**
 * Dummy plugin for U of I iPay payment system.
 */
class UofiIpayDummyPlugin extends SuccessfulTransactionPlugin
{
    public function processes($paymentSystemName)
    {
        return 'uofi_ipay' === $paymentSystemName;
    }
}
