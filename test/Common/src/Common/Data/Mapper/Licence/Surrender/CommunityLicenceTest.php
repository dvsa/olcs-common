<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\CommunityLicence;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CommunityLicenceTest extends MockeryTestCase
{

    /**
     * @dataProvider resultData
     *
     * @param $apiData
     * @param $formData
     */
    public function testMapFromResult($apiData, $formData)
    {
        static::assertEquals(
            $formData,
            CommunityLicence::mapFromResult($apiData)
        );
    }

    /**
     * @dataProvider  resultData
     * @param $apiData
     * @param $formData
     */
    public function testMapFromForm($apiData, $formData)
    {
        $apiData['communityLicenceDocumentStatus'] = $apiData['communityLicenceDocumentStatus']['id'];

        static::assertEquals(
            $apiData,
            CommunityLicence::mapFromForm($formData)
        );
    }

    public function resultData()
    {
        return [
            'possession' => [
                'apiData' => [

                    "communityLicenceDocumentStatus" => ['id' => 'doc_sts_destroyed'],
                    "communityLicenceDocumentInfo" => null,

                ],
                'formData' => [
                    'communityLicenceDocument' => [
                        'communityLicenceDocument' => 'possession'
                    ]
                ]
            ],
            'lost' => [
                'apiData' => [

                    "communityLicenceDocumentStatus" => ['id' => 'doc_sts_lost'],
                    "communityLicenceDocumentInfo" => 'lost info',

                ],
                'formData' => [
                    'communityLicenceDocument' => [
                        'communityLicenceDocument' => 'lost',
                        'lostContent' => [
                            'details' => 'lost info'
                        ]
                    ]
                ]
            ],
            'stolen' => [
                'apiData' => [

                    "communityLicenceDocumentStatus" => ['id' => 'doc_sts_stolen'],
                    "communityLicenceDocumentInfo" => 'stolen info',

                ],
                'formData' => [
                    'communityLicenceDocument' => [
                        'communityLicenceDocument' => 'stolen',
                        'stolenContent' => [
                            'details' => 'stolen info'
                        ]
                    ]
                ]
            ],
        ];
    }

    public function getStatusForId(string $id)
    {
        switch ($id) {
            case 'doc_sts_destroyed':
                return 'possession';
            case 'doc_sts_lost':
                return 'lost';
            case 'doc_sts_stolen':
                return 'stolen';
        }
        return '';
    }
}
