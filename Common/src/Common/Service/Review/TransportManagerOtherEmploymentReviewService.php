<?php

/**
 * Transport Manager Other Employment Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Transport Manager Other Employment Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerOtherEmploymentReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        return [
            'subSections' => [
                [
                    'mainItems' => $this->formatOtherEmployments($data)
                ]
            ]
        ];
    }

    private function formatOtherEmployments($data)
    {
        if (empty($data['transportManager']['employments'])) {
            return [
                [
                    'freetext' => $this->translate('tm-review-other-employment-none')
                ]
            ];
        }

        $mainItems = [];

        foreach ($data['transportManager']['employments'] as $employment) {
            $mainItems[] = [
                'header' => $employment['employerName'],
                'multiItems' => [
                    [
                        [
                            'label' => 'tm-review-other-employment-address',
                            'value' => $this->formatFullAddress($employment['contactDetails']['address'])
                        ],
                        [
                            'label' => 'tm-review-other-employment-position',
                            'value' => $employment['position']
                        ],
                        [
                            'label' => 'tm-review-other-employment-hours-per-week',
                            'value' => $employment['hoursPerWeek']
                        ]
                    ]
                ]
            ];
        }

        return $mainItems;
    }
}
