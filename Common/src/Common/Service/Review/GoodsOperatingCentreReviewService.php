<?php

/**
 * Goods Operating Centre Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Goods Operating Centre Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOperatingCentreReviewService extends AbstractReviewService
{
    /**
     * Format the OC config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = $this->getServiceLocator()->get('Review\PsvOperatingCentre')->getConfigFromData($data);

        $config['multiItems']['vehicles+trailers'][] = [
            'label' => 'review-operating-centre-total-trailers',
            'value' => $data['noOfTrailersRequired']
        ];

        // Add the advertisements fields
        $config['multiItems']['advertisements'] = [
            [
                'label' => 'review-operating-centre-advertisement-ad-placed',
                'value' => $this->formatYesNo($data['adPlaced'])
            ]
        ];

        if ($data['adPlaced'] === 'Y') {
            $config['multiItems']['advertisements'] = array_merge(
                $config['multiItems']['advertisements'],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-newspaper',
                        'value' => $data['adPlacedIn']
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-date',
                        'value' => $this->formatDate($data['adPlacedDate'])
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-file',
                        'noEscape' => true,
                        'value' => $this->formatAdDocumentList($data)
                    ]
                ]
            );
        }

        return $config;
    }

    private function formatAdDocumentList($data)
    {
        $files = [];

        foreach ($data['operatingCentre']['adDocuments'] as $document) {
            if ($document['application']['id'] == $data['id']) {
                $files[] = $document['filename'];
            }
        }

        if (empty($files)) {
            return $this->translate('no-files-uploaded');
        }

        return implode('<br>', $files);
    }
}
