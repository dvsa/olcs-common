<?php

namespace Common\Form\Elements\Custom;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\LessThan;
use Zend\Validator\StringLength;

class EcmtNoOfPermitsElement extends Text implements InputProviderInterface
{
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
                    ]
                ],
                [
                    'name' => Digits::class,
                    'options' => [
                        'break_chain_on_failure' => true,
                        'messages' => [
                            Digits::NOT_DIGITS => 'permits.page.no-of-permits.error.not-whole-number'
                        ]
                    ]
                ],
                [
                    'name' => LessThan::class,
                    'options' => [
                        'max' => $this->options['max'],
                        'inclusive' => true,
                        'messages' => [
                            LessThan::NOT_LESS_INCLUSIVE => $this->options['maxExceededErrorMessage']
                        ]
                    ]
                ]
            ],
        ];
    }
}
