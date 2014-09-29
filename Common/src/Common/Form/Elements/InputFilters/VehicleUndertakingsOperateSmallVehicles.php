<?php

/**
 * VehicleUndertakingsOperateSmallVehicles
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesValidator;

/**
 * VehicleUndertakingsOperateSmallVehicles
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsOperateSmallVehicles extends Textarea implements InputProviderInterface
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
            new VehicleUndertakingsOperateSmallVehiclesValidator()
        );
    }
}
