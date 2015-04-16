<?php

/**
 * Application Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Application Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesVehicle implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface,
    BusinessRuleAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessRuleAwareTrait;

    protected $licenceVehicleRule = 'ApplicationGoodsVehiclesLicenceVehicle';

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $mode = $params['mode'];
        $id = $params['id'];
        $licenceId = $params['licenceId'];

        $data = $this->getBusinessRuleManager()->get('GoodsVehiclesVehicle')->validate($params['data'], $mode);

        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        $savedVehicle = $this->getServiceLocator()->get('Entity\Vehicle')->save($data);

        $vehicleId = (isset($savedVehicle['id']) ? $savedVehicle['id'] : $data['id']);

        $licenceVehicle = $this->getBusinessRuleManager()
            ->get($this->licenceVehicleRule)
            ->validate($licenceVehicle, $mode, $vehicleId, $licenceId, $id);

        $saved = $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($licenceVehicle);

        $licenceVehicleId = null;

        if (isset($saved['id'])) {
            $licenceVehicleId = $saved['id'];
        } elseif (!empty($licenceVehicle['id'])) {
            $licenceVehicleId = $licenceVehicle['id'];
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $response->setData(['licenceVehicleId' => $licenceVehicleId]);
        return $response;
    }
}
