<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Common\Service\Qa\DateSelect as BaseDateSelect;

class DateSelect extends BaseDateSelect
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        $inputSpecification = $this->callParentGetInputSpecification();

        $inputSpecification['validators'][] = [
            'name' => DateBeforeValidator::class,
            'options' => [
                'dateMustBeBefore' => $this->options['dateMustBeBefore']
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
