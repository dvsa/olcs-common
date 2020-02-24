<?php

/**
 * Traffic Area Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\DataServiceException;
use Common\Service\Data\ListDataInterface;

/**
 * Traffic Area Entity Service
 *
 * @NOTE I implement ListDataInterface here so I can use this service for DynamicSelect
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaEntityService extends AbstractEntityService implements ListDataInterface
{
    const NORTH_EASTERN_TRAFFIC_AREA_CODE    = 'B';
    const NORTH_WESTERN_TRAFFIC_AREA_CODE    = 'C';
    const WEST_MIDLANDS_TRAFFIC_AREA_CODE    = 'D';
    const EASTERN_TRAFFIC_AREA_CODE          = 'F';
    const WELSH_TRAFFIC_AREA_CODE            = 'G';
    const WESTERN_TRAFFIC_AREA_CODE          = 'H';
    const SE_MET_TRAFFIC_AREA_CODE           = 'K';
    const SCOTTISH_TRAFFIC_AREA_CODE         = 'M';
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    protected $entity = 'TrafficArea';

    /**
     * Get Traffic Area value options for select element
     *
     * @return array
     */
    public function getValueOptions()
    {
        $trafficArea = $this->get(array());

        $valueOptions = array();
        $results = $trafficArea['Results'];

        if (empty($results)) {
            throw new DataServiceException('No traffic area value options found');
        }

        foreach ($results as $value) {
            // Skip Northern Ireland Traffic Area
            if ($value['id'] == self::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                continue;
            }

            $valueOptions[$value['id']] = $value['name'];
        }

        asort($valueOptions);

        return $valueOptions;
    }

    public function fetchListOptions($context, $useGroups = false)
    {
        $trafficArea = $this->get(array());

        $valueOptions = array();
        $results = $trafficArea['Results'];

        foreach ($results as $value) {
            $valueOptions[$value['id']] = $value['name'];
        }

        asort($valueOptions);

        return $valueOptions;
    }
}
