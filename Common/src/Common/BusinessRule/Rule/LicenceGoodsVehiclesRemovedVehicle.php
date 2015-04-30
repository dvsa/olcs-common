<?php

/**
 * Licence Goods Vehicles Removed Vehicle Rule
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Licence Goods Vehicles Removed Vehicle Rule
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceGoodsVehiclesRemovedVehicle implements BusinessRuleInterface, BusinessRuleAwareInterface
{
    use BusinessRuleAwareTrait;

    public function validate($data)
    {
        $validData = null;

        if (isset($data['removalDate'])) {
            $removalDate = $this->getBusinessRuleManager()
                ->get('CheckDate')
                ->validate($data['removalDate']);
            if ($removalDate) {
                $validData = [
                    'id' => $data['id'],
                    'version' => $data['version'],
                    'removalDate' => $removalDate
                ];
            }
        }
        return $validData;
    }
}
