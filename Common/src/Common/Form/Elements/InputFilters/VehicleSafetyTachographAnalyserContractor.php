<?php
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * @deprecated This does not get used and must be removed as in: OLCS-15198
 *
 * VehicleSafetyTachographAnalyserContractor
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
