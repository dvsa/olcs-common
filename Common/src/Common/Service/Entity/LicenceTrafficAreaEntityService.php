<?php

/**
 * Licence version of the Traffic Area Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence version of the Traffic Area Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceTrafficAreaEntityService extends TrafficAreaEntityService
{
    /**
     * Application traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'properties' => array(),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                    'name'
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
        'properties' => array(
            'id',
            'version'
        )
    );

    /**
     * Get traffic area for licence
     *
     * @param int $licenceId
     * @return string
     */
    public function getTrafficArea($licenceId)
    {
        if ($this->trafficArea === null) {
            $licence = $this->getServiceLocator()->get('Helper\Rest')
                ->makeRestCall('Licence', 'GET', $licenceId, $this->trafficAreaBundle);

            if (isset($licence['trafficArea'])) {
                $this->trafficArea = $licence['trafficArea'];
            }
        }

        return $this->trafficArea;
    }

    /**
     * Get licence details to update traffic area
     *
     * @return array
     */
    protected function getLicenceDetailsToUpdateTrafficArea($identifier)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('Licence', 'GET', $identifier, $this->licDetailsForTaBundle);
    }
}
