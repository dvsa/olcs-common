<?php

namespace Common\Form\Elements\Custom;

use Common\Service\Qa\Custom\Ecmt\NoOfPermitsSingleValidator;
use Zend\Validator\GreaterThan;

class EcmtNoOfPermitsSingleElement extends EcmtNoOfPermitsElement
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        $inputSpecification = $this->callParentGetInputSpecification();

        $inputSpecification['validators'][] = [
            'name' => GreaterThan::class,
            'options' => [
                'min' => 0,
                'messages' => [
                    GreaterThan::NOT_GREATER => self::GENERIC_ERROR_KEY
                ]
            ]
        ];

        $inputSpecification['validators'][] = [
            'name' => NoOfPermitsSingleValidator::class,
            'options' => [
                'maxPermitted' => $this->options['maxPermitted'],
                'permitsRemaining' => $this->options['permitsRemaining'],
                'emissionsCategory' => $this->options['emissionsCategory']
            ]
        ];

        return $inputSpecification;
    }

    /**
     * Call getInputSpecification from parent class (to assist with unit testing)
     *
     * @return array
     */
    protected function callParentGetInputSpecification()
    {
        return parent::getInputSpecification();
    }
}
