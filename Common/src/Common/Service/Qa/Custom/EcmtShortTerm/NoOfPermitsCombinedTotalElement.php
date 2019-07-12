<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\Form\Element\Hidden;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Callback;

class NoOfPermitsCombinedTotalElement extends Hidden implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMax'],
                        'callbackOptions' => [$this->options['maxPermitted']],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda-ecmt-short-term.number-of-permits.error.total-max-exceeded'
                        ]
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMin'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda-ecmt-short-term.number-of-permits.error.total-min-exceeded'
                        ]
                    ]
                ],
            ],
        ];
    }
}
