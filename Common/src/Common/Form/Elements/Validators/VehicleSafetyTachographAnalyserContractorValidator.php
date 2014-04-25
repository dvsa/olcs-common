<?php

/**
 * VehicleSafetyTachographAnalyserContractorValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * VehicleSafetyTachographAnalyserContractorValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleSafetyTachographAnalyserContractorValidator extends AbstractValidator
{
    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        $this->error($value);
        print '<pre>';
        print_r($context);
        print '</pre>';
        return false;
    }
}
