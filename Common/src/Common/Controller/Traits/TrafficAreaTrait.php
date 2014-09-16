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
    private $trafficArea;

    /**
     * Get Traffic Area information for current application
     *
     * @return array
     */
    protected function getTrafficArea()
    {
        if (!$this->trafficArea) {
            $bundle = array(
                'properties' => array(
                    'id',
                    'version',
                ),
                'children' => array(
                    'licence' => array(
                        'properties' => array(
                            'id'
                        ),
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

            $application = $this->makeRestCall(
                'Application',
                'GET',
                array(
                    'id' => $this->getIdentifier(),
                ),
                $bundle
            );
            if (is_array($application) && array_key_exists('licence', $application) &&
                is_array($application['licence']) &&
                array_key_exists('trafficArea', $application['licence'])) {
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
        $bundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id',
                        'version'
                    )
                )
            )
        );
        $application = $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), $bundle);
        if (is_array($application) && array_key_exists('licence', $application) &&
            array_key_exists('version', $application['licence'])) {
            $data = array(
                        'id' => $application['licence']['id'],
                        'version' => $application['licence']['version'],
                        'trafficArea' => $id
            );
            $this->makeRestCall('Licence', 'PUT', $data);
            if ($id) {
                $licenceService = $this->getLicenceService();
                $licenceService->generateLicence($this->getIdentifier());
            }
        }
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
                    return strcmp($a["name"], $b["name"]);
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

    /**
     * Get licence service
     *
     * @return Common\Service\Licence\Licence
     */
    public function getLicenceService()
    {
        return $this->getServiceLocator()->get('licence');
    }
}
