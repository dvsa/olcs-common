<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Validators\NoOfPermitsCombinedTotalValidator;
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
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.max-exceeded'
                        ]
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMin'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.min-exceeded'
                        ]
                    ]
                ],
            ],
        ];
    }
}