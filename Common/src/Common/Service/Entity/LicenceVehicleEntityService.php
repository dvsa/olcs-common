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
        // We optionally alter the bundle, so we need a local var
        $bundle = $this->vehicleDataBundle;

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

        if (isset($filters['specifiedDate'])) {
            $query['specifiedDate'] = $filters['specifiedDate'];
        }

        if (isset($filters['vrm'])) {
            $bundle['children']['vehicle']['required'] = true;
            $bundle['children']['vehicle']['criteria']['vrm'] = $filters['vrm'];
        }

        if (isset($filters['removalDate'])) {
            $query[] = ['removalDate' => $filters['removalDate']];
        }

        // If we want to have active discs
        // Where goodsDisc->discNo NOT NULL
        // AND there are some goodsDiscRecords

        // Apply a method of filtering by discs
        if (isset($filters['disc'])) {
            if ($filters['disc'] === 'Y') {
                $bundle['children']['goodsDiscs']['required'] = true;
                $bundle['children']['goodsDiscs']['critieria']['ceasedDate'] = 'NULL';
                $bundle['children']['goodsDiscs']['critieria']['issuedDate'] = 'NOT NULL';
            }

            // @todo Actually need to say where NO discs have a discNo, rather than if any disc has no disc no
            if ($filters['disc'] === 'N') {

                $bundle['children']['goodsDiscs']['requireNone'] = true;
                $bundle['children']['goodsDiscs']['critieria']['ceasedDate'] = 'NULL';
                $bundle['children']['goodsDiscs']['critieria']['issuedDate'] = 'NOT NULL';
            }
        }

        $pagination = [
            'page' => isset($filters['page']) ? $filters['page'] : 1,
            'limit' => isset($filters['limit']) ? $filters['limit'] : 10,
        ];

        $query = array_merge($query, $pagination);

        return $this->get($query, $bundle);
    }
}
