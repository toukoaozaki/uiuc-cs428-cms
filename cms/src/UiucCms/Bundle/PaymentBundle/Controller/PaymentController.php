<?php

namespace UiucCms\Bundle\PaymentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\PluginController\Result;
use JMS\Payment\CoreBundle\Entity\Payment;
use JMS\DiExtraBundle\Annotation as DI;
use UiucCms\Bundle\PaymentBundle\Entity\Order;


class PaymentController
{
    private $templating;
    private $formFactory;
    private $router;
    private $ppc;
    private $em;
    private $securityContext;

    public function __construct(
        $templating,
        $formFactory,
        $router,
        $ppc,
        $em,
        $securityContext
    ) {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->ppc = $ppc;
        $this->em = $em;
        $this->securityContext = $securityContext;
    }

    /**
     * @Template
     */
    public function choosePaymentAction(Request $request, Order $order)
    {
        $owner = $order->getOwner();
        if (null !== $owner && $owner != $this->getUser()) {
            // user mismatch
            throw new AccessDeniedHttpException();
        }
        $form = $this->getFormFactory()->create(
            'jms_choose_payment_method',
            null,
            array(
                'amount' => $order->getAmount(),
                'currency' => $order->getCurrency(),
                'predefined_data' => array(
                    'uofi_payment' => array(
                        'return_url' => $this->router->generate(
                            'uiuc_cms_payment_complete',
                            array('order' => $order->getOrderNumber())
                        ),
                    ),
                ),
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
                        array('order' => $order->getOrderNumber())
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
        $instruction = $order->getPaymentInstruction();
        $pending_transaction = $instruction->getPendingTransaction();
        if (null === $pending_transaction) {
            // start new transaction
            $payment = $this->ppc->createPayment(
                $instruction->getId(),
                $instruction->getAmount() - $instruction->getDepositedAmount()
            );
        } else {
            $payment = $pending_transaction->getPayment();
        }

        // ask the payment controller to deposit the transaction
        $result = $this->ppc->approveAndDeposit(
            $payment->getId(),
            $payment->getTargetAmount()
        );

        switch ($result->getStatus()) {
            case Result::STATUS_SUCCESS:
                // payment successful
                $this->ppc->closePaymentInstruction($instruction);
                return $this->renderPaymentSuccess($order, $result);
            case Result::STATUS_PENDING:
                $except = $result->getPluginException();
                if ($except instanceof ActionRequiredException) {
                    $action = $except->getAction();

                    if ($action instanceof VisitUrl) {
                        // need user to visit the requested page
                        return new RedirectResponse($action->getUrl());
                    }
                }
                // unknown exception
            default:
                return $this->renderPaymentFailure($order, $result);
        }
    }

    protected function renderPaymentFailure($order, $result)
    {
        return $this->render(
            'UiucCmsPaymentBundle:Payment:paymentFailure.html.twig',
            array(
                'order' => $order,
                'result' => $result,
                'return_url' => $order->getReturnUrl()
            )
        );
    }

    protected function renderPaymentSuccess($order, $result)
    {
        return $this->render(
            'UiucCmsPaymentBundle:Payment:paymentSuccess.html.twig',
            array(
                'order' => $order,
                'result' => $result,
                'return_url' => $order->getReturnUrl()
            )
        );
    }

    protected function render($template, $args = array())
    {
        $content = $this->getTemplatingEngine()->render(
            $template, $args
        );
        return new Response($content);
    }

    protected function getTemplatingEngine()
    {
        return $this->templating;
    }

    protected function getFormFactory()
    {
        return $this->formFactory;
    }

    protected function getUser()
    {
        $token = $this->securityContext->getToken();
        if (null !== $token) {
            return $token->getUser();
        }
        return null;
    }

}
