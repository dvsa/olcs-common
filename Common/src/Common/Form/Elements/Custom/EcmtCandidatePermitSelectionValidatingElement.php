<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Validators\EcmtCandidatePermitSelectionValidator;
use Zend\Form\Element\Hidden;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Callback;

class EcmtCandidatePermitSelectionValidatingElement extends Hidden implements InputProviderInterface
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
                            EcmtCandidatePermitSelectionValidator::class,
                            'validate'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.irhp.candidate-permit-selection.error'
                        ]
                    ],
                ],
            ],
        ];
    }
}
