<?php

namespace UiucCms\Bundle\PaymentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaymentController
{
    private $router;
    private $formFactory;
    private $ppc;
    private $em;

    public function __construct(
        $router,
        $formFactory,
        $ppc,
        $em
    ) {
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->ppc = $ppc;
        $this->em = $em;
    }

    /**
     * @Template
     */
    public function choosePaymentAction(Request $request, Order $order)
    {
        $form = $this->formFactory->create(
            'jms_choose_payment_method',
            null,
            array(
                'amount' => $order->getAmount(),
                'currency' => $order->getCurrency()
            )
        );

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                // validation passed; hand it to the next controller action
                $instruction = $form->getData();
                $this->ppc->createPaymentInstruction($instruction);
                $order->setPaymentInstruction($instruction);
                $this->em->persist($order);
                $this->em->flush($order);
                return new RedirectResponse(
                    $this->router->generate(
                        'uiuc_cms_payment_complete',
                        array('order' => $order)
                    )
                );
            }
        }

        return array(
            'order' => $order,
            'form' => $form->createView()
        );
    }

    public function completePaymentAction(Request $request, Order $order)
    {
        throw new AccessDeniedHttpException('invalid access');
    }

    public function capturePaymentAction(Request $request)
    {
        throw new AccessDeniedHttpException('invalid access');
    }
}
