<?php
// src/UiucCms/UserBundle/Form/Type/UserType.php
namespace UiucCms\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options)
     {
          $builder->add('email', 'email');
          $builder->add('plainPassword', 'repeated', array(
               'first_name'  => 'password',
               'second_name' => 'confirm_password',
               'type'        => 'password',
          ));
          $builder->add('first_name', 'text');
          $builder->add('last_Name', 'text');
          $builder->add('phone', 'text');
          $builder->add('register', 'submit');
     }

     public function setDefaultOptions(OptionsResolverInterface $resolver)
     {
          $resolver->setDefaults(array(
               'data_class' => 'UiucCms\Bundle\UserBundle\Entity\User'
          ));
     }

     public function getName()
     {
          return 'user';
     }
}
?>
