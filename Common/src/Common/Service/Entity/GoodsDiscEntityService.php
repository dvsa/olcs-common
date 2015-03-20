<?php

/**
 * Goods Disc Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Goods Disc Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsDiscEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'GoodsDisc';

    public function updateExistingForLicence($licenceId, $applicationId)
    {
        $vehicleService = $this->getServiceLocator()->get('Entity\LicenceVehicle');

        $vehicles = $vehicleService->getExistingForLicence($licenceId, $applicationId);

        // @TODO: multi update
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();
        foreach ($vehicles as $vehicle) {
            if (isset($vehicle['goodsVehicle']) && $vehicle['goodsVehicle']['ceasedDate'] === null) {
                $vehicleService->forceUpdate($vehicle['goodsVehicle']['id'], ['ceasedDate' => $date]);
            }
        }

        $this->createForVehicles($vehicles);
    }

    public function createForVehicles($licenceVehicles)
    {
        $defaults = array(
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        foreach ($licenceVehicles as $licenceVehicle) {
            $data = array_merge(
                $defaults,
                array(
                    'licenceVehicle' => $licenceVehicle['id']
                )
            );
            $this->save($data);
        }
    }
}
