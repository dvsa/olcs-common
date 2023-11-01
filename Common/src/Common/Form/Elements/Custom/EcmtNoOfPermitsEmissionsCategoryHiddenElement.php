<?php

namespace Common\Form\Elements\Custom;

use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Identical;

class EcmtNoOfPermitsEmissionsCategoryHiddenElement extends Hidden implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification(): array
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
