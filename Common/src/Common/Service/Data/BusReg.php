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

    public function fetchVariationHistory($id = null) {
        $variationBundle = [
            'properties' => 'ALL',
            'children' => [
                'busNoticePeriod' => [
                    'properties' => 'ALL'
                ],
                'status' => [
                    'properties' => 'ALL'
                ],
                'busServiceTypes'
            ]
        ];
        $id = 1;
        $params['sort'] = 'variationNo';
        $params['order'] = 'DESC';
        //$data = $this->loadCurrent();
        //$params['routeNo'] = $data['routeNo'];

        $busRegList = $this->fetchList($params, $variationBundle);

       return $busRegList;

    }
}
