<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\CurrentDiscs;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CurrentDiscsTest extends MockeryTestCase
{
    /**
     * @dataProvider dpMapFromResult
     */
    public function testMapFromResult($apiData, $formData)
    {
        static::assertEquals(
            $formData,
            CurrentDiscs::mapFromResult($apiData)
        );
    }

    /**
     * @dataProvider dpMapFromForm
     */
    public function testMapFromForm($apiData, $formData)
    {
        static::assertEquals(
            $apiData,
            CurrentDiscs::mapFromForm($formData)
        );
    }

    public function dpMapFromResult()
    {
        return [
            [
                'apiData' => [
                    "id" => 5,
                    "licence_id" => 70,
                    "status" => "surr_sts_start",
                    "discDestroyed" => 1,
                    "discLost" => 2,
                    "discStolen" => 3,
                    "discLostInfo" => 'it was lost',
                    "discStolenInfo" => 'someone stole it',
                    "licenceDocumentStatus" => null,
                    "communityLicenceDocumentStatus" => null,
                    "digitalSignatureId" => null,
                    "signatureType" => null,
                    "createdBy" => 542,
                    "lastModifiedBy" => 542,
                    "createdOn" => "2018-12-17 12 =>20 =>17",
                    "lastModifiedOn" => null,
                    "version" => 1
                ],
                'formData' => [
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'Y',
                        'info' => [
                            'number' => 1,
                        ],
                    ],
                    'lostSection' => [
                        'lost' => 'Y',
                        'info' => [
                            'number' => 2,
                            'details' => 'it was lost',
                        ],
                    ],
                    'stolenSection' => [
                        'stolen' => 'Y',
                        'info' => [
                            'number' => 3,
                            'details' => 'someone stole it',
                        ],
                    ],
                ]
            ],
            [
                'apiData' => [
                    "id" => 5,
                    "licence_id" => 70,
                    "status" => "surr_sts_start",
                    "discDestroyed" => null,
                    "discLost" => null,
                    "discStolen" => null,
                    "discLostInfo" => null,
                    "discStolenInfo" => null,
                    "licenceDocumentStatus" => null,
                    "communityLicenceDocumentStatus" => null,
                    "digitalSignatureId" => null,
                    "signatureType" => null,
                    "createdBy" => 542,
                    "lastModifiedBy" => 542,
                    "createdOn" => "2018-12-17 12 =>20 =>17",
                    "lastModifiedOn" => null,
                    "version" => 1
                ],
                'formData' => [
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'N',
                        'info' => [
                            'number' => null,
                        ],
                    ],
                    'lostSection' => [
                        'lost' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null,
                        ],
                    ],
                    'stolenSection' => [
                        'stolen' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null,
                        ],
                    ],
                ]
            ]
        ];
    }

    public function dpMapFromForm()
    {
        return [
            [
                'apiData' => [
                    "discDestroyed" => 1,
                    "discLost" => 2,
                    "discStolen" => 3,
                    "discLostInfo" => 'it was lost',
                    "discStolenInfo" => 'someone stole it',
                ],
                'formData' => [
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'Y',
                        'info' => [
                            'number' => 1,
                        ],
                    ],
                    'lostSection' => [
                        'lost' => 'Y',
                        'info' => [
                            'number' => 2,
                            'details' => 'it was lost',
                        ],
                    ],
                    'stolenSection' => [
                        'stolen' => 'Y',
                        'info' => [
                            'number' => 3,
                            'details' => 'someone stole it',
                        ],
                    ],
                ]
            ],
            [
                'apiData' => [

                ],
                'formData' => [
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'N',
                        'info' => [
                            'number' => null,
                        ],
                    ],
                    'lostSection' => [
                        'lost' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null,
                        ],
                    ],
                    'stolenSection' => [
                        'stolen' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null,
                        ],
                    ],
                ]
            ]
        ];
    }


}
