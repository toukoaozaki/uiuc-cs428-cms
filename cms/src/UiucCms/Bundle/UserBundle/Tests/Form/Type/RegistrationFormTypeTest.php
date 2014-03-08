<?php
// Code mostly derived from http://symfony.com/doc/current/cookbook/form/unit_testing.html
namespace UiucCms\Bundle\UserBundle\Tests\Form\Type;

use UiucCms\Bundle\UserBundle\Form\Type\RegistrationFormType;
use UiucCms\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
{
    // Tests Symfony form object
    public function testSubmitValidData()
    {
        // password is not included; it should be tested with web-based
        // functional tests involving controllers.
        $formData = array(
            'firstName' => 'John',
            'lastName' => 'Doe',
            'username' => 'johndoe01',
            'email' => 'johndoe01@johndoe.com');
        // form to be tested
        $formType = new RegistrationFormType('UiucCms\Bundle\UserBundle\Entity\User');
        $form = $this->factory->create($formType);

        // test submission handling; in the app, this happens in the controller
        $form->submit($formData);
        // check errors on data transformation
        $this->assertTrue($form->isSynchronized());
        // check whether data matches expected
        $result = $form->getData();
        $this->assertEquals($formData['firstName'], $result->getFirstName());
        $this->assertEquals($formData['lastName'], $result->getLastName());
        $this->assertEquals($formData['username'], $result->getUsername());
        $this->assertEquals($formData['email'], $result->getEmail());
    }

    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }
}
?>
