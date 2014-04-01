<?php

namespace UiucCms\Bundle\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DummyFailureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function getName()
    {
        return 'dummy_failure';
    }
}
