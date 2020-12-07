<?php

namespace Common\Form\Elements\Custom;

use Laminas\Form\Element as ZendElement;
use Laminas\InputFilter\InputProviderInterface;

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
                new \Laminas\Filter\StringTrim(),
            ],
            'validators' => [
                [
                    'name' => \Laminas\Validator\StringLength::class,
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
