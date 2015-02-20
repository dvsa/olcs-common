<?php

/**
 * Psv Operating Centre Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Psv Operating Centre Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvOperatingCentreReviewService extends AbstractReviewService
{
    /**
     * Format the OC config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return [
            'header' => $this->formatShortAddress($data['operatingCentre']['address']),
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centre-address',
                        'value' => $this->formatFullAddress($data['operatingCentre']['address'])
                    ]
                ],
                'vehicles+trailers' => [
                    [
                        'label' => 'review-operating-centre-total-vehicles',
                        'value' => $data['noOfVehiclesRequired']
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-sufficient-parking',
                        'value' => $this->formatConfirmed($data['sufficientParking'])
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-permission',
                        'value' => $this->formatConfirmed($data['permission'])
                    ]
                ]
            ]
        ];
    }
}
