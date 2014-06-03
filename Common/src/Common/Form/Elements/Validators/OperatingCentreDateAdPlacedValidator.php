<?php

/**
 * OperatingCentreDateAdPlacedValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Common\Form\Elements\Validators\Date;

/**
 * OperatingCentreDateAdPlacedValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreDateAdPlacedValidator extends Date
{
    /**
     * Cross field validation
     *
     * @param string $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = array())
    {
        if ($context['adPlaced'] != 'Y') {
            return true;
        }

        return parent::isValid($value, $context);
    }
}
