<?php

namespace UiucCms\Bundle\ConferenceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EnrollmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('arema', 'choice', array(
            'choices' => array(
                'non-vegie' => 'non-vegetarian',
                'vegetarian' => 'vegetarian'
            ), 
            'preferred' => array('none'), 
            'empty_value' => 'Choose your food',
            'empty_data'  => 'none'
            )
        );
        $builder->add('abstract', 'textarea', array('attr' => array('height' => '200')));
        $builder->add('enroll', 'submit');
    }

    public function getName() 
    {
        return 'arema';
    }

}


