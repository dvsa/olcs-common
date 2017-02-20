<?php

namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;

/**
 * Vrm field for vehicles from any country
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class VehicleVrmAny extends ZendElement implements InputProviderInterface
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
                new \Zend\Filter\StringTrim(),
            ],
            'validators' => [
                [
                    'name' => \Zend\Validator\StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
            ],
        ];

        return $specification;
    }
}
