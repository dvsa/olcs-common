<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\CommunityLicence;
use Common\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CommunityLicenceTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestMapFromForm
     */
    public function testMapFromForm($formData, $mappedData)
    {
        static::assertEquals(
            $mappedData,
            CommunityLicence::mapFromForm($formData)
        );
    }

    /**
     * @dataProvider dpTestMapFromResult
     */
    public function testMapFromResult($mappedApiData, $apiData)
    {
        static::assertEquals(
            $mappedApiData,
            CommunityLicence::mapFromResult($apiData)
        );
    }

    public function dpTestMapFromForm()
    {
        return [
            'case_01' =>
                [
                    'form_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'lost',
                                    'lostContent' =>
                                        [
                                            'details' => 'lost info'
                                        ],
                                    'stolenContent' =>
                                        [
                                            'details' => ''
                                        ],
                                ],
                        ],
                    'mapped_form_data' =>
                        [
                            'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                            'communityLicenceDocumentInfo' => 'lost info'
                        ],
                ],
            'case_02' =>
                [
                    'form_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'stolen',
                                    'stolenContent' =>
                                        [
                                            'details' => 'stolen info'
                                        ],
                                    'lostContent' =>
                                        [
                                            'details' => ''
                                        ],
                                ],
                        ],
                    'mapped_form_data' =>
                        [
                            'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                            'communityLicenceDocumentInfo' => 'stolen info'
                        ],
                ],
            'case_03' =>
                [
                    'form_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'possession',
                                    'lostContent' =>
                                        [
                                            'details' => 'lost info'
                                        ],
                                    'stolenContent' =>
                                        [
                                            'details' => ''
                                        ],
                                ],

                        ],
                    'mapped_form_data' =>
                        [
                            'communityLicenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                            'communityLicenceDocumentInfo' => null
                        ],
                ],
        ];
    }

    public function dpTestMapFromResult()
    {
        return [
            'case_01' =>
                [
                    'mapped_api_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'lost',
                                    'lostContent' =>
                                        [
                                            'details' => 'lost info'
                                        ]
                                ],
                        ],
                    'api_data' => [
                        'communityLicenceDocumentStatus' =>
                            [
                                'id' => RefData::SURRENDER_DOC_STATUS_LOST,
                            ],
                        'communityLicenceDocumentInfo' => 'lost info'
                    ],
                ],
            'case_02' =>
                [
                    'mapped_api_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'stolen',
                                    'stolenContent' =>
                                        [
                                            'details' => 'stolen info'
                                        ]
                                ],
                        ],
                    'api_data' =>
                        [
                            'communityLicenceDocumentStatus' =>
                                [
                                    'id' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                                ],
                            'communityLicenceDocumentInfo' => 'stolen info'
                        ],
                ],
            'case_03' =>
                [
                    'mapped_api_data' =>
                        [
                            'communityLicence' =>
                                [
                                    'communityLicenceDocument' => 'possession',
                                ],
                        ],
                    'api_data' =>
                        [
                            'communityLicenceDocumentStatus' =>
                                [
                                    'id' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                                ],
                            'communityLicenceDocumentInfo' => null
                        ],
                ]
        ];
    }
}
