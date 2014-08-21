<?php

/**
 * OperatingCentreVehicleAuthorisations
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;
use Common\Form\Elements\Validators\OperatingCentreVehicleAuthorisationsValidator;

/**
 * OperatingCentreVehicleAuthorisations
 */
class OperatingCentreVehicleAuthorisations extends Text implements InputProviderInterface
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
            new OperatingCentreVehicleAuthorisationsValidator(),
            new ZendValidator\Between(array('min' => 0, 'max' => 1000000))
        );
    }
}
