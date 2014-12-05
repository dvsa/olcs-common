<?php

/**
 * Traffic Area Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Traffic Area Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaEntityService extends AbstractEntityService
{
    /**
     * Northern Ireland Traffic Area Code
     */
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
            throw new Exceptions\UnexpectedResponseException('No traffic area value options found');
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
}
