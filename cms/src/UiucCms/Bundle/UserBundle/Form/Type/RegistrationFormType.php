<?php

namespace UiucCms\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // custom fields for User
        $builder->add('firstName');
        $builder->add('lastName');
    }

    public function getName()
    {
        return 'uiuc_cms_user_registration';
    }
}

?>
