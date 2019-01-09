<?php

namespace Common\Data\Mapper\licence\Surrender;

use Common\RefData;
use Common\Data\Mapper\MapperInterface;

class OperatorLicence implements MapperInterface
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
                'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                'licenceDocumentInfo' => null
            ],
            'lost' => [
                'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                'licenceDocumentInfo' => $formData['operatorLicenceDocument']['lostContent']['details']
            ],
            'stolen' => [
                'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                'licenceDocumentInfo' => $formData['operatorLicenceDocument']['stolenContent']['details']
            ],
        ];

        $test = $mappedData[$formData['operatorLicenceDocument']['licenceDocument']];
        return $mappedData[$formData['operatorLicenceDocument']['licenceDocument']];
    }

    public static function mapFromResult(array $data)
    {
        $licenceDocumentStatus = $data["licenceDocumentStatus"]["id"];

        $formData = [
            RefData::SURRENDER_DOC_STATUS_DESTROYED =>
                [
                    'operatorLicenceDocument' => [
                        'licenceDocument' => 'possession'
                    ]
                ],
            RefData::SURRENDER_DOC_STATUS_LOST =>
                [
                    'operatorLicenceDocument' => [
                        'licenceDocument' => 'lost',
                        'lostContent' => [
                            'details' => $data["licenceDocumentInfo"]
                        ]
                    ]
                ],
            RefData::SURRENDER_DOC_STATUS_STOLEN =>
                [
                    'operatorLicenceDocument' => [
                        'licenceDocument' => 'stolen',
                        'stolenContent' => [
                            'details' => $data["licenceDocumentInfo"]
                        ]
                    ]
                ],
        ];

        return $formData[$licenceDocumentStatus];
    }
}
