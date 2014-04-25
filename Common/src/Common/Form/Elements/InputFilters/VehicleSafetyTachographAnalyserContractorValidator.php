<?php

/**
 * VehicleSafetyTachographAnalyserContractorValidator
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * VehicleSafetyTachographAnalyserContractorValidator
 */
class VehicleSafetyTachographAnalyserContractorValidator extends Text implements InputProviderInterface
{
    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new VehicleSafetyTachographAnalyserContractorValidator()
        );
    }
}
