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

    public function fetchDetail($id = null) {
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

    public function fetchVariationHistory($routeNo) {
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
