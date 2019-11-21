<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Validators\EcmtNoOfPermitsCombinedTotalValidator;
use Zend\Form\Element\Hidden;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Callback;

class EcmtNoOfPermitsCombinedTotalElement extends Hidden implements InputProviderInterface
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
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateNonZeroValuePresent'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.no-fields-populated'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateMultipleNonZeroValuesNotPresent'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.two-or-more-fields-populated'
                        ]
                    ]
                ],
            ],
        ];
    }
}
