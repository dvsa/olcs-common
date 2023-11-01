<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Validators\EcmtNoOfPermitsCombinedTotalValidator;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Callback;

class EcmtNoOfPermitsCombinedTotalElement extends Hidden implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification(): array
    {
        return [
            'name' => $this->getName(),
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateMax'
                        ],
                        'callbackOptions' => [$this->options['maxPermitted']],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda.ecmt.number-of-permits.error.total-max-exceeded'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateMin'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda.ecmt.number-of-permits.error.total-min-exceeded'
                        ]
                    ]
                ],
            ],
        ];
    }
}
