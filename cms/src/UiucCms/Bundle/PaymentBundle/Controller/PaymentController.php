<?php

namespace UiucCms\Bundle\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaymentController
{
    public function startPaymentAction($paymentId)
    {
        throw new AccessDeniedHttpException('invalid access');
    }


    public function capturePaymentAction(Request $request)
    {
        throw new AccessDeniedHttpException('invalid access');
    }
}
