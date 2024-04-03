<?php

namespace Common\Form\Elements\Custom;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Digits;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

class EcmtNoOfPermitsElement extends Text implements InputProviderInterface
{
    public const GENERIC_ERROR_KEY = 'qanda.ecmt.number-of-permits.error.enter-permits-needed';

    public const MAX_LENGTH = '4';

    protected $attributes = [
        'maxLength' => self::MAX_LENGTH
    ];

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification(): array
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
