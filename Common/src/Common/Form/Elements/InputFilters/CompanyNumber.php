<?php

namespace Common\Form\Elements\InputFilters;

use Zend\I18n\Validator\Alnum;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\StringLength;

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
class CompanyNumber extends \Zend\Form\Element implements InputProviderInterface
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
                            StringLength::TOO_LONG => 'common.form.validation.company_number.too_long',
                        ]
                    ]
                ],
                [
                    'name' => 'Alnum',
                    'options' => [
                        'messages' => [
                             Alnum::NOT_ALNUM => 'common.form.validation.company_number.not_alnum',
                        ],
                    ],
                ]
            ]
        ];

        return $specification;
    }
}
