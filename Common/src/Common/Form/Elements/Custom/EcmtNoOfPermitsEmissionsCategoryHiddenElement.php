<?php

namespace Common\Form\Elements\Custom;

use Zend\Form\Element\Hidden;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Identical;

class EcmtNoOfPermitsEmissionsCategoryHiddenElement extends Hidden implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                [
                    'name' => Identical::class,
                    'options' => [
                        'token' => $this->options['expectedValue'],
                    ]
                ]
            ]
        ];
    }
}
