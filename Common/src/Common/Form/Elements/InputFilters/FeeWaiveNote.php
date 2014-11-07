<?php

/**
 * Fee waive note
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

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
                    'name' => '\Zend\Validator\StringLength',
                    'options'=> [
                        'min' => 5,
                        'max' => 255,
                        'messages' => [
                             \Zend\Validator\StringLength::TOO_SHORT =>
                                'You must enter reason for the waiver. Please enter a minimum of 5 characters'
                        ],
                    ]
                ],
                [
                    'name' => '\Zend\Validator\NotEmpty',
                    'options'=> [
                        'type' => \Zend\Validator\NotEmpty::NULL
                    ]
                ]    
            ]
        ];

        return $specification;
    }
}
