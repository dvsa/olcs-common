<?php

/**
 * Licence Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Licence Goods Vehicles Licence Vehicle Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesLicenceVehicle implements
    BusinessRuleInterface,
    BusinessRuleAwareInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessRuleAwareTrait;

    public function validate($data, $mode, $vehicleId, $licenceId, $id)
    {
        $data['vehicle'] = $vehicleId;

        if ($mode !== 'add') {
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

        if (isset($data['specifiedDate'])) {
            $checkDate = $this->getBusinessRuleManager()->get('CheckDate');
            $data['specifiedDate'] = $checkDate->validate($data['specifiedDate']);
        }

        if (!isset($data['specifiedDate'])) {
            $data['specifiedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d');
        }

        if (isset($data['removalDate'])) {
            $checkDate = $this->getBusinessRuleManager()->get('CheckDate');
            $data['removalDate'] = $checkDate->validate($data['removalDate']);

            if ($data['removalDate'] === null) {
                unset($data['removalDate']);
            }
        }

        return $data;
    }
}
