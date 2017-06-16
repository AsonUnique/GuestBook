<?php

namespace Application\Form;

use Application\Module;
use Zend\Filter\File\RenameUpload;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Button;
use Zend\Form\Element\Email;
use Zend\Form\Element\File;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\Validator\StringLength;

class PostForm extends Form implements InputFilterProviderInterface
{
    /**
     * PostForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'post-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->addElements();
    }

    /**
     * Добавление элементов в форму
     */
    private function addElements()
    {
        $this->add([
            'name' => 'email',
            'type' => Email::class,
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'Enter valid email'
            ],
            'options' => [
                'label' => 'Your Email'
            ]
        ]);

        $this->add([
            'name' => 'message',
            'type' => Textarea::class,
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'Enter your message'
            ],
            'options' => [
                'label' => 'Your Message'
            ]
        ]);

        $this->add([
            'name' => 'avatar',
            'type' => File::class,
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Your Avatar'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Button::class,
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
            ],
            'options' => [
                'label' => 'Send',
            ],
        ]);
    }

    /**
     * Добавление фильтров и валидаторов к форме
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 5,
                            'max' => 255,
                        ],
                    ],
                    [
                        'name' => EmailAddress::class,
                    ],
                ],
            ],
            'message' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
            ],
            'avatar' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => RenameUpload::class,
                        'options' => [
                            'target'               => Module::AVATAR_UPLOAD_PATH_ABS . DIRECTORY_SEPARATOR . 'avatar_',
                            'randomize'            => true,
                            'use_upload_extension' => true,
                        ],

                    ]
                ],
                'validators' => [
                    [
                        'name' => UploadFile::class
                    ],
                    [
                        'name' => IsImage::class
                    ],
                    [
                        'name' => Size::class,
                        'options' => [
                            'max' => '1MB',
                        ],
                    ],
                ],
            ],
        ];
    }
}

