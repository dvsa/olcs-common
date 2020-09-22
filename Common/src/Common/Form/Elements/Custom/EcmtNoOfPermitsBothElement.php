<?php

namespace Common\Form\Elements\Custom;

use Common\Filter\NotPopulatedStringToZero;
use Common\Service\Qa\Custom\Ecmt\NoOfPermitsBothValidator;

class EcmtNoOfPermitsBothElement extends EcmtNoOfPermitsElement
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        $inputSpecification = $this->callParentGetInputSpecification();

        $inputSpecification['filters'][] = [
            'name' => NotPopulatedStringToZero::class
        ];

        $inputSpecification['validators'][] = [
            'name' => NoOfPermitsBothValidator::class,
            'options' => [
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
