<?php

/**
 * VehicleSafetyTachographAnalyserContractor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * VehicleSafetyTachographAnalyserContractor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleSafetyTachographAnalyserContractor extends Text implements InputProviderInterface
{
    protected $continueIfEmpty = true;
    protected $allowEmpty = false;

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
