<?php

/**
 * OperatingCentreTrailerAuthorisations
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\OperatingCentreTrailerAuthorisationsValidator;

/**
 * OperatingCentreTrailerAuthorisations
 */
class OperatingCentreTrailerAuthorisations extends Text implements InputProviderInterface
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
            new OperatingCentreTrailerAuthorisationsValidator()
        );
    }
}
