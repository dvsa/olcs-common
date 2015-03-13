<?php

/**
 * Application Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesPsvReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $mainItems = [
            [
                'multiItems' => [
                    [
                        [
                            'label' => 'application-review-vehicles-hasEnteredReg',
                            'value' => $this->formatYesNo($data['hasEnteredReg'])
                        ]
                    ]
                ]
            ]
        ];

        return [
            'subSections' => [
                [
                    'mainItems' => $this->getServiceLocator()->get('Review\VehiclesPsv')
                        ->getConfigFromData($data, $mainItems)
                ]
            ]
        ];
    }
}
