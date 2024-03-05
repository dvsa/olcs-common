<?php

namespace Common\Form\Elements\InputFilters;

use Laminas\I18n\Validator\Alnum;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\StringLength;

/**
 * Company Number
 *
 * @author Someone <someone@valtech.co.uk>
 */
class CompanyNumber extends \Laminas\Form\Element implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => \Laminas\Filter\StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => \Laminas\Validator\StringLength::class,
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
