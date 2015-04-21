<?php

/**
 * Transport Manager Previous Conviction Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Transport Manager Previous Conviction Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerPreviousConvictionReviewService extends AbstractReviewService
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
                    'mainItems' => $this->formatConvictions($data)
                ]
            ]
        ];
    }

    private function formatConvictions($data)
    {
        if (empty($data['transportManager']['previousConvictions'])) {
            return [
                [
                    'freetext' => $this->translate('tm-review-previous-conviction-none')
                ]
            ];
        }

        $mainItems = [];

        foreach ($data['transportManager']['previousConvictions'] as $conviction) {
            $mainItems[] = [
                'header' => $conviction['categoryText'],
                'multiItems' => [
                    [
                        [
                            'label' => 'tm-review-previous-conviction-date',
                            'value' => $this->formatDate($conviction['convictionDate'], 'd/m/Y')
                        ],
                        [
                            'label' => 'tm-review-previous-conviction-offence',
                            'value' => $conviction['categoryText']
                        ],
                        [
                            'label' => 'tm-review-previous-conviction-offence-details',
                            'value' => $conviction['notes']
                        ],
                        [
                            'label' => 'tm-review-previous-conviction-court',
                            'value' => $conviction['courtFpn']
                        ],
                        [
                            'label' => 'tm-review-previous-conviction-penalty',
                            'value' => $conviction['penalty']
                        ]
                    ]
                ]
            ];
        }

        return $mainItems;
    }
}
