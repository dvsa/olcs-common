<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\Form\Element\MultiCheckbox;

class RestrictedCountriesMultiCheckbox extends MultiCheckbox
{
    public function getInputSpecification(): array
    {
        $spec = parent::getInputSpecification();
        $spec['required'] = false;

        return $spec;
    }
}
