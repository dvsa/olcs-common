<?php

/**
 * NumberOfVehicles
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;

/**
 * NumberOfVehicles
 */
class NumberOfVehicles extends Text implements InputProviderInterface
{
    protected $continueIfEmpty = true;
    protected $allowEmpty = true;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new ZendValidator\Between(array('min' => 0, 'max' => 1000000)),
        );
    }
}
