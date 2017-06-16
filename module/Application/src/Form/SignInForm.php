<?php

namespace Application\Form;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class SignInForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'sign-in', array $options = [])
    {
        parent::__construct($name, $options);

        $this->addElements();
    }

    private function addElements()
    {
        $this->add([
            'name'       => 'login',
            'type'       => Text::class,
            'attributes' => [
                'required' => true,
                'class'    => 'form-control',
            ],
            'options'    => [
                'label' => 'Login',
            ],
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => Password::class,
            'attributes' => [
                'required' => true,
                'class'    => 'form-control',
            ],
            'options'    => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'attributes' => [
                'class' => 'btn btn-default',
                'value' => 'Submit'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'login' => [
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
            ],
        ];
    }
}