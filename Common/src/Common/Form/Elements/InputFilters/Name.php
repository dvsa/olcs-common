<?php

/**
 * Name Filter
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Name Filter
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Name extends ZendElement implements InputProviderInterface
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
                ['name' => 'Zend\Validator\StringLength', 'options' => ['min' => 2, 'max' => 35]]
            ]
        ];

        return $specification;
    }
}
