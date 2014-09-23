<?php

/**
 * Trait to handle work with Traffic Area
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Controller\Traits;

/**
 * Trait to handle work with Traffic Area
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait TrafficAreaTrait
{

    /**
     * Holds the Traffic Area details
     *
     * @var array
     */
    protected $trafficArea;

    /**
     * Licence details for traffic area
     *
     * @var array
     */
    protected $applicationLicenceDetailsForTrafficAreaBundle = array(
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
     * Application traffic area bundle
     *
     * @var array
     */
    protected $applicationTrafficAreaBundle = array(
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
     * Get Traffic Area information for current application
     *
     * @return array
     */
    protected function getTrafficArea()
    {
        if (empty($this->trafficArea)) {
            $application = $this->makeRestCall(
                'Application',
                'GET',
                array(
                    'id' => $this->getIdentifier(),
                ),
                $this->applicationTrafficAreaBundle
            );

            if (isset($application['licence']['trafficArea'])) {
                $this->trafficArea = $application['licence']['trafficArea'];
            }
        }

        return $this->trafficArea;
    }

    /**
     * Set traffic area to application's licence based on traarea id
     *
     * @param string $id
     */
    public function setTrafficArea($id = null)
    {
        $licenceDetails = $this->getLicenceDetailsToUpdateTrafficArea();

        if (isset($licenceDetails['version'])) {

            $data = array(
                'id' => $licenceDetails['id'],
                'version' => $licenceDetails['version'],
                'trafficArea' => $id
            );

            $this->makeRestCall('Licence', 'PUT', $data);

            if ($id) {
                $licenceService = $this->getServiceLocator()->get('licence');
                $licenceService->generateLicence($this->getIdentifier());
            }
        }
    }

    /**
     * Get licence details to update traffic area
     *
     * @return array
     */
    protected function getLicenceDetailsToUpdateTrafficArea()
    {
        $application = $this->makeRestCall(
            'Application',
            'GET',
            array(
                'id' => $this->getIdentifier()
            ),
            $this->applicationLicenceDetailsForTrafficAreaBundle
        );

        return $application['licence'];
    }

    /**
     * Get Traffic Area value options for select element
     *
     * @return array
     */
    protected function getTrafficValueOptions()
    {
        $bundle = array(
            'properties' => array(
                'id',
                'name',
            ),
        );

        $trafficArea = $this->makeRestCall('TrafficArea', 'GET', array(), $bundle);

        $valueOptions = array();
        $results = $trafficArea['Results'];

        if (is_array($results) && count($results)) {
            usort(
                $results,
                function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                }
            );

            // remove Northern Ireland Traffic Area
            foreach ($results as $key => $value) {
                if ($value['id'] == self::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                    unset($results[$key]);
                    break;
                }
            }

            foreach ($results as $element) {
                $valueOptions[$element['id']] = $element['name'];
            }
        }
        return $valueOptions;
    }
}
