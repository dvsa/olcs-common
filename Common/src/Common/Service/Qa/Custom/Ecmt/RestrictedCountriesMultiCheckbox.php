<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\Form\Element\MultiCheckbox;

class RestrictedCountriesMultiCheckbox extends MultiCheckbox
{
    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();
        $spec['required'] = false;

        return $spec;
    }
}
