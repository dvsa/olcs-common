<?php

/**
 * OperatingCentreDateAdPlaced
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Common\Form\Elements\Custom\DateSelect;
use Common\Form\Elements\Validators\OperatingCentreDateAdPlacedValidator;

/**
 * OperatingCentreDateAdPlaced
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreDateAdPlaced extends DateSelect
{
    protected $required = false;

    protected function getValidator()
    {
        return new OperatingCentreDateAdPlacedValidator(array('format' => 'Y-m-d'));
    }
}
