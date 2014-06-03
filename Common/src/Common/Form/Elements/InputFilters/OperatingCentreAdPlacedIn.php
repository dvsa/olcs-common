<?php

/**
 * OperatingCentreAdPlacedIn
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\OperatingCentreAdPlacedInValidator;

/**
 * OperatingCentreAdPlacedIn
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAdPlacedIn extends Text implements InputProviderInterface
{
    protected $continueIfEmpty = true;
    protected $required = false;
    protected $allowEmpty = false;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(new OperatingCentreAdPlacedInValidator());
    }
}
