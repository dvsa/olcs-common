<?php

/**
 * Fee waive note
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Fee waive note
 */
class FeeWaiveNote extends TexareatMax255Min5 implements InputProviderInterface
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
                ['name' => 'Laminas\Filter\StringTrim'],
            ],
            'validators' => [
                [
                    'name' => '\Laminas\Validator\StringLength',
                    'options'=> [
                        'min' => 5,
                        'max' => 255,
                        'messages' => [
                             \Laminas\Validator\StringLength::TOO_SHORT =>
                                'You must enter reason for the waiver. Please enter a minimum of 5 characters'
                        ],
                    ]
                ],
                [
                    'name' => '\Laminas\Validator\NotEmpty',
                    'options'=> [
                        'type' => \Laminas\Validator\NotEmpty::NULL
                    ]
                ]
            ]
        ];

        return $specification;
    }
}
