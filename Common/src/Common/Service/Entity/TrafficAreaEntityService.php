<?php

/**
 * Traffic Area Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Traffic Area Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TrafficAreaEntityService extends AbstractEntityService
{
    /**
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    /**
     * Holds the Traffic Area details
     *
     * @var string
     */
    protected $trafficArea;

    /**
     * Application traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            )
        )
    );

    /**
     * Licence details for traffic area
     *
     * @var array
     */
    protected $licDetailsForTaBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id',
                    'version'
                )
            )
        )
    );

    /**
     * Traffic area values
     *
     * @var array
     */
    private $trafficAreaValuesBundle = array(
        'properties' => array(
            'id',
            'name'
        )
    );

    /**
     * Cache the value options
     *
     * @var array
     */
    private $valueOptions;

    /**
     * Get Traffic Area information for current application
     *
     * @param int $applicationId
     * @return array
     */
    public function getTrafficArea($applicationId)
    {
        if (empty($this->trafficArea)) {

            $application = $this->getServiceLocator()->get('Helper\Rest')
                ->makeRestCall('Application', 'GET', $applicationId, $this->trafficAreaBundle);

            if (isset($application['licence']['trafficArea'])) {
                $this->trafficArea = $application['licence']['trafficArea'];
            }
        }

        return $this->trafficArea;
    }

    /**
     * Set traffic area to application's licence based on area id
     *
     * @param int $identifier
     * @param int $id
     */
    public function setTrafficArea($identifier, $id = null)
    {
        $licenceDetails = $this->getLicenceDetailsToUpdateTrafficArea($identifier);

        if (isset($licenceDetails['version'])) {

            $data = array(
                'id' => $licenceDetails['id'],
                'version' => $licenceDetails['version'],
                'trafficArea' => $id
            );

            $this->getServiceLocator()->get('Helper\Rest')->makeRestCall('Licence', 'PUT', $data);

            if ($id) {
                $licenceService = $this->getServiceLocator()->get('licence');
                $licenceService->generateLicence($identifier);
            }
        }
    }

    /**
     * Get Traffic Area value options for select element
     *
     * @return array
     */
    public function getTrafficAreaValueOptions()
    {
        if ($this->valueOptions === null) {
            $trafficArea = $this->getServiceLocator()->get('Helper\Rest')
                ->makeRestCall('TrafficArea', 'GET', array(), $this->trafficAreaValuesBundle);

            $this->valueOptions = array();
            $results = $trafficArea['Results'];

            if (!empty($results)) {
                usort(
                    $results,
                    function ($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    }
                );

                foreach ($results as $key => $value) {
                    // Skip Northern Ireland Traffic Area
                    if ($value['id'] == static::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                        continue;
                    }

                    $this->valueOptions[$value['id']] = $value['name'];
                }
            }
        }

        return $this->valueOptions;
    }

    /**
     * Get licence details to update traffic area
     *
     * @return array
     */
    protected function getLicenceDetailsToUpdateTrafficArea($identifier)
    {
        $application = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('Application', 'GET', $identifier, $this->licDetailsForTaBundle);

        return $application['licence'];
    }
}
