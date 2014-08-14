<?php

/**
 * Phone Filter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Phone Filter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Phone extends ZendElement implements InputProviderInterface
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
            'required' => false,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
            ],
            'validators' => [
                [
                    'name'=> 'Zend\Validator\Regex',
                    'options' => [
                        'pattern' => '/^[0-9 ]+$/',
                        'messages' => ['regexNotMatch' => 'The input must contain only digits or spaces']
                    ]
                ],
                ['name' => 'Zend\Validator\StringLength', 'options' => ['min' => 5, 'max' => 20]]
            ]
        ];

        return $specification;
    }
}
