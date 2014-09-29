<?php

/**
 * Licence version of the Traffic Area Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

/**
 * Licence version of the Traffic Area Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTrafficAreaSectionService extends TrafficAreaSectionService
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
    public function getTrafficArea()
    {
        if ($this->trafficArea === null) {
            $licence = $this->getHelperService('RestHelper')
                ->makeRestCall('Licence', 'GET', $this->getIdentifier(), $this->trafficAreaBundle);

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
    protected function getLicenceDetailsToUpdateTrafficArea()
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall('Licence', 'GET', $this->getIdentifier(), $this->licDetailsForTaBundle);
    }
}
