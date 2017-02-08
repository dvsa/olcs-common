<?php

namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class VehiclePlatedWeight extends ZendElement implements InputProviderInterface
{
    /**
     * Get Input Specification
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array_filter(
            [
                'type' => \Zend\InputFilter\Input::class,
                'name' => $this->getName(),
                'required' => $this->getOption('required'),
                'allow_empty' => $this->getOption('allow_empty'),
                'validators' => [
                    [
                        'name' => \Zend\Validator\Digits::class,
                        'options' => [
                            'messages' => [
                                \Zend\Validator\Digits::NOT_DIGITS => 'vehicle.error.platedWeight.notDigits',
                            ],
                        ],
                    ],
                    [
                        'name' => \Zend\Validator\Between::class,
                        'options' => [
                            'min' => 0,
                            'max' => 999999,
                        ],
                    ],
                ],
            ]
        );
    }
}
