<?php

/**
 * Name Filter
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as LaminasElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Name Filter
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Name extends LaminasElement implements InputProviderInterface
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
            'required' => false,
            'filters' => [
                ['name' => 'Laminas\Filter\StringTrim'],
            ],
            'validators' => [
                ['name' => 'Laminas\Validator\StringLength', 'options' => ['min' => 2, 'max' => 35]]
            ]
        ];

        return $specification;
    }
}
