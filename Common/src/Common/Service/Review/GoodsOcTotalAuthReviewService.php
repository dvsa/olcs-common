<?php

/**
 * Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOcTotalAuthReviewService extends AbstractReviewService
{
    /**
     * Get total auth config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => $data['totAuthVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => $data['totAuthTrailers']
                    ]
                ]
            ]
        ];
    }
}
