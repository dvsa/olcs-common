<?php

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
class CompanyNumber extends ZendElement implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'Zend\Validator\StringLength',
                    'options'=> [
                        'min' => 1,
                        'max' => 8,
                        'messages' => [
                            StringLength::TOO_LONG => 'The company number cannot be more than 8 characters'
                        ]
                    ]
                ],
                [
                    'name' => 'Alnum',
                    'options' => [
                        'messages' => [
                             Alnum::NOT_ALNUM =>
                                'Must be 8 digits; alpha-numeric characters allowed; ' .
                                'no special characters; mandatory when displayed'
                        ],
                    ],
                ]
            ]
        ];

        return $specification;
    }
}
