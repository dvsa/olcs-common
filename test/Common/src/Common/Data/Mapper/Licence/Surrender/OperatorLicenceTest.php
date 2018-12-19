<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\OperatorLicence;
use Common\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OperatorLicenceTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestMapFromForm
     */
    public function testMapFromForm($formData, $mappedData)
    {
        static::assertEquals(
            $mappedData,
            OperatorLicence::mapFromForm($formData)
        );
    }

    /**
     * @dataProvider dpTestMapFromApi
     */
    public function testMapFromApi($mappedApiData, $apiData)
    {
        static::assertEquals(
            $mappedApiData,
            OperatorLicence::mapFromApi($apiData)
        );
    }

    public function dpTestMapFromForm()
    {
        return [
            'case_01' =>
                [
                    'form_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => 'lost',
                                    'lostContent' =>
                                        [
                                            'details' => 'lost info'
                                        ]
                                ],
                        ],
                    'mapped_form_data' =>
                        [
                            'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                            'licenceDocumentInfo' => 'lost info'
                        ],
                ],
            'case_02' =>
                [
                    'form_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => 'stolen',
                                    'stolenContent' =>
                                        [
                                            'details' => 'stolen info'
                                        ]
                                ],
                        ],
                    'mapped_form_data' =>
                        [
                            'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                            'licenceDocumentInfo' => 'stolen info'
                        ],
                ],
            'case_03' =>
                [
                    'form_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => 'possession',
                                ],
                        ],
                    'mapped_form_data' =>
                        [
                            'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                            'licenceDocumentInfo' => null
                        ],
                ],
        ];
    }

    public function dpTestMapFromApi()
    {
        return [
            'case_01' =>
                [
                    'mapped_api_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => RefData::SURRENDER_DOC_STATUS_LOST,
                                    'lostContent' =>
                                        [
                                            'details' => 'lost info'
                                        ]
                                ],
                        ],
                    'api_data' => [
                        'licenceDocumentStatus' =>
                            [
                                'id' => RefData::SURRENDER_DOC_STATUS_LOST,
                            ],
                        'licenceDocumentInfo' => 'lost info'
                    ],
                ],
            'case_02' =>
                [
                    'mapped_api_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                                    'stolenContent' =>
                                        [
                                            'details' => 'stolen info'
                                        ]
                                ],
                        ],
                    'api_data' =>
                        [
                            'licenceDocumentStatus' =>
                                [
                                    'id' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                                ],
                            'licenceDocumentInfo' => 'stolen info'
                        ],
                ],
            'case_03' =>
                [
                    'mapped_api_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'licenceDocument' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                                ],
                        ],
                    'api_data' =>
                        [
                            'licenceDocumentStatus' =>
                                [
                                    'id' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                                ],
                        ],
                ]
        ];
    }
}
