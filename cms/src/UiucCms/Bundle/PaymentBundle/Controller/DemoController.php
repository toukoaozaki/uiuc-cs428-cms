<?php

namespace UiucCms\Bundle\PaymentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UiucCms\Bundle\PaymentBundle\Entity\Order;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DemoController extends Controller
{
    /**
     * @Template
     */
    public function demoPaymentAction(Request $request)
    {
        $data = array('amount' => '99.99');
        $form = $this->createFormBuilder($data)
            ->add('amount', 'money', array('currency' => 'USD'))
            ->getForm();

        $form->handleRequest($request);
        $message = '';

        if ($form->isValid()) {
            $data = $form->getData();
            # built-in symfony 'money' field works in floating point
            # TODO: find out a better way to handle currency
            $amount = (string)$data['amount'];
            $message = $amount;
            $order = new Order('USD', $amount);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($order);
            $em->flush();
            return new RedirectResponse(
                $this->get('router')->generate(
                    'uiuc_cms_payment_start',
                    array('order' => $order->getOrderNumber())
                )
            );
        }
        return array(
            'form' => $form->createView(),
            'message' => $message
        );
    }
}
