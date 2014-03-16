<?php

namespace UiucCms\Bundle\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PaymentController
{
    public function startPaymentAction($paymentId)
    {
        throw new AccessDeniedException('invalid access');
    }


    public function capturePaymentAction(Request $request)
    {
        throw new AccessDeniedException('invalid access');
    }
}
