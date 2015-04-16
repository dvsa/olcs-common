<?php

/**
 * Application Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Application Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesLicenceVehicle implements BusinessRuleInterface, BusinessRuleAwareInterface
{
    use BusinessRuleAwareTrait;

    public function validate($data, $mode, $vehicleId, $licenceId, $applicationId)
    {
        return $this->getBusinessRuleManager()->get('VariationGoodsVehiclesLicenceVehicle')
            ->validate($data, $mode, $vehicleId, $licenceId, $applicationId);
    }
}
