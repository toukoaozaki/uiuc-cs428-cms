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
        $builder->add('register_begin_date', 'datetime');
        $builder->add('register_end_date', 'datetime');
        $builder->add('topics', 'collection', array('type' => 'text'));
        $builder->add('create', 'submit');
    }

    public function getName() 
    {
        return 'conference';
    }

}

