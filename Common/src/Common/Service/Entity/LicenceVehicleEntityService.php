<?php

/**
 * Licence Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicleEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'LicenceVehicle';

    protected $vehicleBundle = array(
        'children' => array(
            'goodsDiscs',
            'vehicle'
        )
    );

    /**
     * Disc pending bundle
     */
    protected $discBundle = array(
        'children' => array(
            'goodsDiscs'
        )
    );

    /**
     * Holds the bundle to retrieve VRM
     *
     * @var array
     */
    protected $vrmBundle = array(
        'children' => array(
            'vehicle'
        )
    );


    protected $currentVrmBundle = array(
        'children' => array(
            'vehicle'
        )
    );

    protected $vehiclePsvBundle = array(
        'children' => array(
            'vehicle' => array(
                'children' => array(
                    'psvType'
                )
            )
        )
    );

    protected $vehicleDataBundle = array(
        'children' => array(
            'goodsDiscs' => [

            ],
            'interimApplication',
            'vehicle' => [

            ]
        )
    );

    public function getVehicle($id)
    {
        return $this->get($id, $this->vehicleBundle);
    }

    public function getVehiclePsv($id)
    {
        return $this->get($id, $this->vehiclePsvBundle);
    }

    /**
     * Delete functionality just sets the removal date for licence vehicle
     *
     * @param int $id
     */
    public function delete($id)
    {
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $this->forceUpdate($id, array('removalDate' => $date));
    }

    /**
     * Cease the active disc
     *
     * @param int $id
     */
    public function getActiveDiscs($id)
    {
        return $this->get($id, $this->discBundle);
    }

    public function getDiscPendingData($id)
    {
        return $this->get($id, $this->discBundle);
    }

    public function getVrm($id)
    {
        return $this->get($id, $this->vrmBundle)['vehicle']['vrm'];
    }

    public function getCurrentVrmsForLicence($licenceId)
    {
        $data = array(
            'licence' => $licenceId,
            'removalDate' => 'NULL'
        );

        $results = $this->getAll($data, $this->currentVrmBundle);

        $vrms = array();

        foreach ($results['Results'] as $row) {
            $vrms[] = $row['vehicle']['vrm'];
        }

        return $vrms;
    }

    public function getForApplicationValidation($licenceId, $applicationId)
    {
        $query = array(
            'licence' => $licenceId,
            'removalDate' => 'NULL',
            'application' => $applicationId
        );

        $results = $this->getAll($query);

        return $results['Results'];
    }

    /**
     * Fetch all results against a licence which are valid and NOT related
     * to the given application
     */
    public function getExistingForLicence($licenceId, $applicationId)
    {
        $query = [
            'licence' => $licenceId,
            'specifiedDate' => 'NOT NULL',
            'removalDate' => 'NULL',
            'interimApplication' => 'NULL',
            'application' => '!= ' . $applicationId
        ];

        $results = $this->getAll($query, $this->discBundle);

        return $results['Results'];
    }

    /**
     * Fetch all results related to the given application
     */
    public function getExistingForApplication($applicationId)
    {
        $query = [
            'removalDate' => 'NULL',
            'application' => $applicationId
        ];

        $results = $this->getAll($query, $this->discBundle);

        return $results['Results'];
    }

    public function removeVehicles(array $ids = array())
    {
        $removalDate = $this->getServiceLocator()->get('Helper\Date')->getDate();
        $data = [];

        foreach ($ids as $id) {
            $data[] = [
                'id' => $id,
                'removalDate' => $removalDate,
                '_OPTIONS_' => ['force' => true]
            ];
        }

        return $this->multiUpdate($data);
    }

    public function removeForApplication($applicationId)
    {
        $licenceVehicles = $this->getExistingForApplication($applicationId);
        $ids = [];
        foreach ($licenceVehicles as $lv) {
            $ids[] = $lv['id'];
        }
        return $this->removeVehicles($ids);
    }

    public function getVehiclesDataForApplication($applicationId, array $filters = array())
    {
        // If we want to show specified vehicles, then we need to widen our search to included the licence too
        if (isset($filters['specifiedDate']) && $filters['specifiedDate'] === 'NOT NULL') {

            // then our query needs to be where...
            $query = [
                // either ...
                [
                    // application id matches...
                    'application' => $applicationId,
                    // OR licence id matches...
                    'licence' => $this->getServiceLocator()->get('Entity\Application')
                        ->getLicenceIdForApplication($applicationId),
                ]
            ];

        } else {
            // Otherwise just filter by application
            $query = [
                'application' => $applicationId
            ];
        }

        $query = $this->buildVehiclesDataQuery($query, $filters);
        $bundle = $this->buildVehiclesDataBundle($filters);

        if (isset($filters['specifiedDate'])) {
            $query['specifiedDate'] = $filters['specifiedDate'];
        }

        return $this->get($query, $bundle);
    }

    public function getVehiclesDataForVariation($applicationId, array $filters = array())
    {
        // then our query needs to be where...
        $query = [
            // either ...
            [
                // application id matches...
                'application' => $applicationId,
                // OR licence id matches...
                'licence' => $this->getServiceLocator()->get('Entity\Application')
                    ->getLicenceIdForApplication($applicationId),
            ]
        ];

        $query = $this->buildVehiclesDataQuery($query, $filters);
        $bundle = $this->buildVehiclesDataBundle($filters);

        if (isset($filters['specifiedDate'])) {
            $query['specifiedDate'] = $filters['specifiedDate'];
        }

        return $this->get($query, $bundle);
    }

    public function getVehiclesDataForLicence($licenceId, array $filters = array())
    {
        $query = ['licence' => $licenceId, 'specifiedDate' => 'NOT NULL'];

        $query = $this->buildVehiclesDataQuery($query, $filters);
        $bundle = $this->buildVehiclesDataBundle($filters);

        return $this->get($query, $bundle);
    }

    protected function buildVehiclesDataQuery($query, $filters)
    {
        if (isset($filters['removalDate'])) {
            $query['removalDate'] = $filters['removalDate'];
        }

        $pagination = [
            'page' => isset($filters['page']) ? $filters['page'] : 1,
            'limit' => isset($filters['limit']) ? $filters['limit'] : 10,
        ];

        return array_merge($query, $pagination);
    }

    protected function buildVehiclesDataBundle($filters)
    {
        $bundle = $this->vehicleDataBundle;

        if (isset($filters['vrm'])) {
            $bundle['children']['vehicle']['required'] = true;
            $bundle['children']['vehicle']['criteria']['vrm'] = $filters['vrm'];
        }

        if (isset($filters['disc']) && in_array($filters['disc'], ['Y', 'N'])) {

            $bundle['children']['goodsDiscs']['criteria']['ceasedDate'] = 'NULL';
            $bundle['children']['goodsDiscs']['criteria']['issuedDate'] = 'NOT NULL';

            if ($filters['disc'] === 'Y') {
                $bundle['children']['goodsDiscs']['required'] = true;
            }

            if ($filters['disc'] === 'N') {
                $bundle['children']['goodsDiscs']['requireNone'] = true;
            }
        }

        return $bundle;
    }
}
