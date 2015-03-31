<?php

/**
 * Variation Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;

/**
 * Variation Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehiclesLicenceVehicle implements BusinessRuleInterface
{
    public function validate($data, $mode, $vehicleId, $licenceId, $applicationId)
    {
        $data['application'] = $applicationId;

        $data['vehicle'] = $vehicleId;

        if ($mode !== 'add') {
            unset($data['specifiedDate']);
            unset($data['removedDate']);
            unset($data['discNo']);
        } else {
            $data['licence'] = $licenceId;
        }

        if (isset($data['receivedDate'])) {
            $checkDate = $this->getBusinessRuleManager()->get('CheckDate');
            $data['receivedDate'] = $checkDate->validate($data['receivedDate']);

            if ($data['receivedDate'] === null) {
                unset($data['receivedDate']);
            }
        }

        return $data;
    }
}
