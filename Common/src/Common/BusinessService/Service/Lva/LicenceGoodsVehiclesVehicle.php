<?php

/**
 * Licence Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\Response;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Licence Goods Vehicles Vehicle
 * - Inherits the majority of it's logic from the application version, just optionally requests a disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesVehicle extends ApplicationGoodsVehiclesVehicle implements BusinessServiceAwareInterface
{
    use BusinessServiceAwareTrait;

    protected $licenceVehicleRule = 'LicenceGoodsVehiclesLicenceVehicle';

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $response = parent::process($params);

        $data = $response->getData();
        $licenceVehicleId = $data['licenceVehicleId'];

        if ($params['mode'] == 'add' && !empty($licenceVehicleId)) {

            $response = $this->getBusinessServiceManager()->get('Lva\RequestDisc')
                ->process(['licenceVehicle' => $licenceVehicleId, 'isCopy' => 'N']);

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
