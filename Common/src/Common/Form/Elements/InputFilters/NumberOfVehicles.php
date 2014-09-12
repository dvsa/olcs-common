<?php

/**
 * NumberOfVehicles
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;
use Common\Form\Elements\Validators\OperatingCentreAuthorisationValidator;

/**
 * NumberOfVehicles
 */
class NumberOfVehicles extends Text implements InputProviderInterface
{
    protected $allowEmpty = false;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new ZendValidator\Between(array('min' => 0, 'max' => 1000000)),
            new OperatingCentreAuthorisationValidator(),
        );
    }
}
