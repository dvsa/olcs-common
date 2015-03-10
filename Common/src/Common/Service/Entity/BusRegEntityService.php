<?php

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusRegEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'BusReg';

    /**
     * Main data bundle
     *
     * @var array
     */
    private $mainDataBundle = array(
        'children' => array(
            'licence',
            'status'
        )
    );

    /**
     * Fee data bundle
     *
     * @var array
     */
    private $feeDataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'licenceType',
                    'trafficArea'
                )
            )
        )
    );

    /**
     * Get data for task processing
     *
     * @param int $id
     * @return array
     */
    public function getDataForTasks($id)
    {
        return $this->get($id, $this->mainDataBundle);
    }

    /**
     * Get data for fee processing
     *
     * @param int $id
     * @return array
     */
    public function getDataForFees($id)
    {
        return $this->get($id, $this->feeDataBundle);
    }

    public function findByIdentifier($identifier)
    {
        $params = [
            'regNo' => $identifier,
            'limit' => 1
        ];

        /**
         * The rules for fetching bus regs by reg number are complex.
         * OLCS-6334 (https://jira.i-env.net/browse/OLCS-6334) implements these
         * requirements using a view, so we hook into that here to get an ID first
         */
        $result = $this->getServiceLocator()
            ->get('Helper\Rest')
            ->makeRestCall('BusRegSearchView', 'GET', $params);

        if ($result['Count'] === 0) {
            return false;
        }

        /**
         * However, we then do a straight lookup on the ID we got back to guarantee
         * that the caller of this method gets a bus reg entity with all its properties.
         * This also allows us to define and manipulate bundles as normal, so the extra
         * GET request is a worthwhile tradeoff.
         */
        return $this->get($result['Results'][0]['id'], $this->mainDataBundle);
    }

    /**
     * This method exists for EBSR which requires the most recent variation, not the most recent active variation
     * to prevent regression when the backend service is implemented this method implements an order by on a different
     * field.
     *
     * @TODO in the event of a refused variation, the previous record should be returned instead
     *
     * @param $identifier
     * @return bool
     */
    public function findMostRecentByIdentifier($identifier)
    {
        $params = [
            'regNo' => $identifier,
            'sort'  => 'id',
            'order' => 'DESC'
        ];

        $result = $this->get($params, $this->mainDataBundle);
        if ($result['Count'] === 0) {
            return false;
        }

        return $result['Results'][0];
    }

    /**
     * Find the most recent Route No by Licence
     * Assumes that Route Numbers are incremental
     *
     * @param $licenceId
     * @return array
     */
    public function findMostRecentRouteNoByLicence($licenceId)
    {
        $params = [
            'licence' => $licenceId,
            'sort'  => 'routeNo',
            'order' => 'DESC',
            'limit' => 1
        ];

        $result = $this->get($params);
        if ($result['Count'] === 0) {
            return false;
        }

        return $result['Results'][0];
    }
}
