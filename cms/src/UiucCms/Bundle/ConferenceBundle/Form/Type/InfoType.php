<?php

namespace UiucCms\Bundle\ConferenceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('food', 'choice', array(
            'choices' => array(
                'non-vegie' => 'non-vegetarian',
                'vegetarian' => 'vegetarian'
            ), 
            'preferred_choices' => array('none'), 
            'empty_value' => 'Choose your food',
            'empty_data'  => 'none'
            )
        );
        $builder->add('abstract', 'textarea', array('attr' => array('height' => '500')));
        $builder->add('enroll', 'submit');
    }

    public function getName() 
    {
        return 'info';
    }

}


