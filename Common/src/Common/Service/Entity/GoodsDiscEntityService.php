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
        $vehicles = $this->getServiceLocator()
            ->get('Entity\LicenceVehicle')
            ->getExistingForLicence($licenceId, $applicationId);

        $discsToCease = [];
        foreach ($vehicles as $vehicle) {
            foreach ($vehicle['goodsDiscs'] as $disc) {
                if ($disc['ceasedDate'] === null) {
                    $discsToCease[] = $disc['id'];
                }
            }
        }

        if (!empty($discsToCease)) {
            $this->ceaseDiscs($discsToCease);
        }

        $this->createForVehicles($vehicles);
    }

    public function createForVehicles($licenceVehicles)
    {
        $defaults = [
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N',
            '_OPTIONS_' => ['force' => true]
        ];

        $data = [];
        foreach ($licenceVehicles as $licenceVehicle) {
            $data[] = array_merge(
                $defaults,
                ['licenceVehicle' => $licenceVehicle['id']]
            );
        }

        $this->multiCreate($data);
    }

    public function ceaseDiscs(array $ids = array())
    {
        $ceasedDate = $this->getServiceLocator()->get('Helper\Date')->getDate();
        $data = [];

        foreach ($ids as $id) {
            $data[] = [
                'id' => $id,
                'ceasedDate' => $ceasedDate,
                '_OPTIONS_' => ['force' => true]
            ];
        }

        $this->multiUpdate($data);
    }
}
