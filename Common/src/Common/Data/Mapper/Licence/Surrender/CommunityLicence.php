<?php


namespace Common\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\MapperInterface;
use Common\RefData;

class CommunityLicence implements MapperInterface
{
    /**
     * Map data from form to DTO
     *
     * @param array $formData Form data
     *
     * @return array
     */
    public static function mapFromForm(array $formData): array
    {
        $mappedData = [
            'possession' => [
                'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                'communityLicenceDocumentInfo' => null
            ],
            'lost' => [
                'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                'communityLicenceDocumentInfo' => $formData['communityLicence']['lostContent']['details'] ?? null
            ],
            'stolen' => [
                'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                'communityLicenceDocumentInfo' => $formData['communityLicence']['stolenContent']['details'] ?? null
            ],
        ];
        return $mappedData[$formData['communityLicence']['communityLicenceDocument']];
    }

    public static function mapFromResult(array $data)
    {
        $licenceDocumentStatus = $data["communityLicenceDocumentStatus"]["id"];

        $formData = [
            RefData::SURRENDER_DOC_STATUS_DESTROYED =>
                [
                    'communityLicence' => [
                        'communityLicenceDocument' => 'possession'
                    ]
                ],
            RefData::SURRENDER_DOC_STATUS_LOST =>
                [
                    'communityLicence' => [
                        'communityLicenceDocument' => 'lost',
                        'lostContent' => [
                            'details' => $data["communityLicenceDocumentInfo"]
                        ]
                    ]
                ],
            RefData::SURRENDER_DOC_STATUS_STOLEN =>
                [
                    'communityLicence' => [
                        'communityLicenceDocument' => 'stolen',
                        'stolenContent' => [
                            'details' => $data["communityLicenceDocumentInfo"]
                        ]
                    ]
                ],
        ];

        return $formData[$licenceDocumentStatus];
    }
}
