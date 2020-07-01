<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\GreaterThan;
use Zend\Validator\StringLength;

class NoOfPermitsElement extends Text implements InputProviderInterface
{
    const MAX_LENGTH = 4;
    const ERROR_KEY = 'qanda.bilaterals.number-of-permits.error.enter-permits-required';

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
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => self::MAX_LENGTH,
                        'break_chain_on_failure' => true,
                        'messages' => [
                            StringLength::TOO_SHORT => self::ERROR_KEY,
                            StringLength::TOO_LONG => self::ERROR_KEY
                        ]
                    ]
                ],
                [
                    'name' => Digits::class,
                    'options' => [
                        'break_chain_on_failure' => true,
                        'messages' => [
                            Digits::NOT_DIGITS => self::ERROR_KEY
                        ]
                    ]
                ],
                [
                    'name' => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                        'inclusive' => false,
                        'messages' => [
                            GreaterThan::NOT_GREATER => self::ERROR_KEY
                        ]
                    ]
                ]
            ],
        ];
    }
}
