<?php

namespace Common\Service\Data;

use Common\Service\Data\CrudAbstract;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Service Class Task
 *
 * @package Common\Service\Data
 */
class Application extends CrudAbstract
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $serviceName = 'Application';

    /**
     * Wrapper method to match interface.
     *
     * @param int|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchData($id = null, $bundle = null)
    {
        return $this->fetchApplicationData($id, $bundle);
    }

    /**
     * Fetches application data
     *
     * @param int|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchApplicationData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData($id))) {
            $bundle = is_null($bundle) ? $this->getBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = [
            'children' => [
                'licence',
                'status'
            ]
        ];

        return $bundle;
    }

    /**
     * Bundle to fetch all operating centres for a application
     * @return array
     */
    public function getOperatingCentreBundle()
    {
        return array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre'
                    )
                )
            )
        );
    }

    /**
     * Can this entity have cases
     * @param $id
     * @return bool
     */
    public function canHaveCases($id)
    {
        $application = $this->fetchApplicationData($id);

        if (empty($application['status'])
            || ($application['status']['id'] == ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED)
            || empty($application['licence']) || empty($application['licence']['licNo'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Fetches an array of OperatingCentres for the application.
     * @param null $id
     * @param null $bundle
     * @return array
     */
    public function fetchOperatingCentreData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData('oc_' .$id))) {

            $bundle = is_null($bundle) ? $this->getOperatingCentreBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);

            $this->setData('oc_' .$id, $data);
        }

        return $this->getData('oc_' . $id);
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
