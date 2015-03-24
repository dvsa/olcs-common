<?php

/**
 * Bus Reg service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Common\Service\BusRegistration;

/**
 * Bus Reg service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusReg extends Generic
{
    protected $serviceName = 'BusReg';

    protected $bundle = [
        'children' => [
            'status'
        ]
    ];

    /**
     * Returns whether a bus reg may be granted
     *
     * @param $id
     *
     * @return Bool
     */
    public function isGrantable($id)
    {
        $busReg = $this->fetchOne($id);

        $fields = [
            'timetableAcceptable',
            'mapSupplied',
            'trcConditionChecked',
            'copiedToLaPte',
            'laShortNote',
            'applicationSigned'
        ];

        foreach ($fields as $field) {
            if ($busReg[$field] != 'Y') {
                return false;
            }
        }

        return true;
    }

    /**
     * Fetches details for a busRegId
     *
     * @param null $id
     * @return array
     */
    public function fetchDetail($id)
    {
        $variationBundle = [
            'children' => [
                'licence' => [
                    'children' => [
                        'publicationLinks' => [
                            'children' => [
                                'publication'
                            ]
                        ]
                    ]
                ],
                'subsidised',
                'localAuthoritys',
                'trafficAreas',
                'busNoticePeriod',
                'status',
                'busServiceTypes'
            ]
        ];

        $busRegDetail = $this->fetchOne($id, $variationBundle);

        return $busRegDetail;
    }

    /**
     * Fetches variation history from busReg table by route number
     *
     * @param int $routeNo
     * @return array
     */
    public function fetchVariationHistory($routeNo)
    {
        $variationBundle = [
            'children' => [
                'status'
            ]
        ];

        $params['routeNo'] = $routeNo;
        $params['sort'] = 'variationNo';
        $params['order'] = 'DESC';

        $busRegList = $this->fetchList($params, $variationBundle);

        return $busRegList;
    }

    /**
     * Returns whether a bus reg is the latest variation
     *
     * @param $id
     *
     * @return Bool
     */
    public function isLatestVariation($id)
    {
        $busReg = $this->fetchOne($id);

        if (empty($busReg['regNo'])) {
            // assume that BusReg without regNo is the latest
            return true;
        }

        // get the lastest variation for the regNo which is not Refused or Withdrawn
        $latestActiveVariation = $this->fetchLatestActiveVariation($busReg['regNo']);

        if (empty($latestActiveVariation['id'])) {
            // there's no active variation for the regNo
            return true;
        }

        if ($id == $latestActiveVariation['id']) {
            // id to check matches the latest active variation
            return true;
        }

        return false;
    }

    /**
     * Fetches the lastest variation from busReg table by reg number
     *
     * @param string $regNo
     * @return array
     */
    public function fetchLatestActiveVariation($regNo)
    {
        $variationBundle = [
            'children' => [
                'status'
            ]
        ];

        $params['regNo'] = $regNo;
        $params['sort'] = 'variationNo';
        $params['status']
            = 'NOT IN '.json_encode([BusRegistration::STATUS_REFUSED, BusRegistration::STATUS_WITHDRAWN]);
        $params['order'] = 'DESC';
        $params['limit'] = 1;

        $busRegList = $this->fetchList($params, $variationBundle);

        return !empty($busRegList[0]) ? $busRegList[0] : [];
    }
}
