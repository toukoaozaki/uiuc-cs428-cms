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
                'Non-vegetarian' => 'Non-vegetarian',
                'Vegetarian' => 'Vegetarian'
            ), 
            'preferred_choices' => array('none'), 
            'empty_value' => 'Choose your food',
            'empty_data'  => 'None'
            )
        );
        $builder->add('abstract', 'textarea', 
            array('max_length' => 255, 'attr' => array('cols' => 50, 'rows' => '5')));
        $builder->add('enroll', 'submit');
    }

    public function getName() 
    {
        return 'info';
    }

}


