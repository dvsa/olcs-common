<?php

namespace Common\Form\Elements\Custom;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class EcmtNoOfPermitsElement extends Text implements InputProviderInterface
{
    const GENERIC_ERROR_KEY = 'qanda.ecmt.number-of-permits.error.enter-permits-needed';
    const MAX_LENGTH = 4;

    protected $attributes = [
        'maxLength' => self::MAX_LENGTH
    ];

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => false,
            'continue_if_empty' => true,
            'filters' => [
                [
                    'name' => StringTrim::class
                ]
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'break_chain_on_failure' => true,
                        'messages' => [
                            NotEmpty::IS_EMPTY => self::GENERIC_ERROR_KEY
                        ]
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'max' => self::MAX_LENGTH,
                        'break_chain_on_failure' => true,
                        'messages' => [
                            StringLength::INVALID => self::GENERIC_ERROR_KEY,
                            StringLength::TOO_LONG => self::GENERIC_ERROR_KEY
                        ]
                    ]
                ],
                [
                    'name' => Digits::class,
                    'options' => [
                        'break_chain_on_failure' => true,
                        'messages' => [
                            Digits::NOT_DIGITS => self::GENERIC_ERROR_KEY
                        ]
                    ]
                ]
            ]
        ];
    }
}
