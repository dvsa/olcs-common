<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Validators\EcmtCandidatePermitSelectionValidator;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Callback;

class EcmtCandidatePermitSelectionValidatingElement extends Hidden implements InputProviderInterface
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
                        'callback' => static fn($value, array $context): bool => \Common\Form\Elements\Validators\EcmtCandidatePermitSelectionValidator::validate($value, $context),
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.irhp.candidate-permit-selection.error'
                        ]
                    ],
                ],
            ],
        ];
    }
}
