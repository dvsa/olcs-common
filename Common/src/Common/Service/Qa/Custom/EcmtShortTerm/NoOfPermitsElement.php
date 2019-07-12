<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\LessThan;
use Zend\Validator\StringLength;

class NoOfPermitsElement extends Text implements InputProviderInterface
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
                            Digits::NOT_DIGITS => 'qanda-ecmt-short-term.number-of-permits.error.category-not-whole-number'
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
