<?php

namespace UiucCms\Bundle\ConferenceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('year', 'integer');
        $builder->add('city', 'text');
        $builder->add(
            'register_begin_date',
            'date',
            array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            )
        );
        $builder->add(
            'register_end_date',
            'date',
            array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            )
        );
        $builder->add('topics', 'text');
        $builder->add('max_enrollment', 'integer');
        $builder->add(
            'cover_fee',
            'money',
            array(
                'currency' => 'USD'
            )
        );
        $builder->add('create', 'submit');
    }

    public function getName() 
    {
        return 'conference';
    }

}


