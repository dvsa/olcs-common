<?php

/**
 * Bus Reg service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

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
}
