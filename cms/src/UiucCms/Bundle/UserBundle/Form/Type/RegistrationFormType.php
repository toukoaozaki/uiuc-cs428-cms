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
        $builder->add(
            'firstName',
            null,
            array(
                'label' => 'form.firstname',
            )
        );
        $builder->add(
            'lastName',
            null,
            array(
                'label' => 'form.lastname',
            )
        );
    }

    public function getName()
    {
        return 'uiuc_cms_user_registration';
    }
}

?>
