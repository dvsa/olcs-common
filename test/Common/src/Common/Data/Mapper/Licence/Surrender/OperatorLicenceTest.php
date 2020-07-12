<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\OperatorLicence;
use Common\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OperatorLicenceTest extends MockeryTestCase
{
    private $operatorLicence;

    public function setUp(): void
    {
        $this->operatorLicence = new OperatorLicence();
    }

    /**
     * @dataProvider dpTestMapFromForm
     */
    public function testMapFromForm($formData, $mappedData)
    {
        static::assertEquals(
            $mappedData,
            $this->operatorLicence->mapFromForm($formData)
        );
    }

    /**
     * @dataProvider dpTestMapFromResult
     */
    public function testMapFromResult($mappedApiData, $apiData)
    {
        static::assertEquals(
            $mappedApiData,
            $this->operatorLicence->mapFromResult($apiData)
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
                                    'operatorLicenceDocument' => 'lost',
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
                                    'operatorLicenceDocument' => 'stolen',
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
                                    'operatorLicenceDocument' => 'possession',
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
                            'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                            'licenceDocumentInfo' => null
                        ],
                ],
                'case_04' =>
                [
                    'form_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'operatorLicenceDocument' => 'possession'
                                ],

                        ],
                    'mapped_form_data' =>
                        [
                            'licenceDocumentStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                            'licenceDocumentInfo' => null
                        ],
                ]
        ];
    }

    public function dpTestMapFromResult()
    {
        return [
            'case_01' =>
                [
                    'mapped_api_data' =>
                        [
                            'operatorLicenceDocument' =>
                                [
                                    'operatorLicenceDocument' => 'lost',
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
                                    'operatorLicenceDocument' => 'stolen',
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
                                    'operatorLicenceDocument' => 'possession',
                                ],
                        ],
                    'api_data' =>
                        [
                            'licenceDocumentStatus' =>
                                [
                                    'id' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                                ],
                            'licenceDocumentInfo' => null
                        ],
                ]
        ];
    }
}
