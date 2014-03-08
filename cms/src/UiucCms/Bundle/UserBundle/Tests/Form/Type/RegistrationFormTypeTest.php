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
    private $data;
    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->setUpValidData();
    }

    protected function setUpValidData()
    {
        // password is not included; it should be tested with web-based
        // functional tests involving controllers.
        $this->data = array(
            'firstName' => 'John',
            'lastName' => 'Doe',
            'username' => 'johndoe01',
            'email' => 'johndoe01@johndoe.com');
        // form to be tested
        $formType = new RegistrationFormType('UiucCms\Bundle\UserBundle\Entity\User');
        $form = $this->factory->create($formType);

        // test submission handling; in the app, this happens in the controller
        $form->submit($this->data);
        // check errors on data transformation
        $this->assertTrue($form->isSynchronized());
        $this->form = $form;
    }

    // Tests Symfony form object
    public function testSubmitValidData()
    {
        // check whether data matches expected
        $result = $this->form->getData();
        $this->assertEquals($this->data['firstName'], $result->getFirstName());
        $this->assertEquals($this->data['lastName'], $result->getLastName());
        $this->assertEquals($this->data['username'], $result->getUsername());
        $this->assertEquals($this->data['email'], $result->getEmail());
    }

    // Tests FormView object
    public function testViewValidData()
    {
        $view = $this->form->createView();
        $children = $view->children;
        // test whether FormView contains required fields
        foreach (array_keys($this->data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
        $this->assertArrayHasKey('plainPassword', $children);
    }

    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }
}
?>
