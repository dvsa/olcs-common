<?php

/**
 * Fee waive note
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\InputFilter\InputProviderInterface;

/**
 * Fee waive note
 */
class FeeWaiveNote extends TexareatMax255Min5 implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     */
    public function getInputSpecification(): array
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => \Laminas\Filter\StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => \Laminas\Validator\StringLength::class,
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
                    'name' => \Laminas\Validator\NotEmpty::class,
                    'options'=> [
                        'type' => \Laminas\Validator\NotEmpty::NULL
                    ]
                ]
            ]
        ];
    }
}
