<?php

/**
 * Transport Manager Previous Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Transport Manager Previous Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerPreviousLicenceReviewService extends AbstractReviewService
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
                    'mainItems' => $this->formatLicences($data)
                ]
            ]
        ];
    }

    private function formatLicences($data)
    {
        if (empty($data['transportManager']['otherLicences'])) {
            return [
                [
                    'freetext' => $this->translate('tm-review-previous-licence-none')
                ]
            ];
        }

        $mainItems = [];

        foreach ($data['transportManager']['otherLicences'] as $licence) {
            $mainItems[] = [
                'header' => $licence['licNo'],
                'multiItems' => [
                    [
                        [
                            'label' => 'tm-review-previous-licence-licNo',
                            'value' => $licence['licNo']
                        ],
                        [
                            'label' => 'tm-review-previous-licence-holder',
                            'value' => $licence['holderName']
                        ]
                    ]
                ]
            ];
        }

        return $mainItems;
    }
}
