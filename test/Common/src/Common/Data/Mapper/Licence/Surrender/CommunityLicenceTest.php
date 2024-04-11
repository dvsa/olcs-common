<?php

namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\CommunityLicence;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CommunityLicenceTest extends MockeryTestCase
{
    private $communityLicence;

    protected function setUp(): void
    {
        $this->communityLicence = new CommunityLicence();
    }

    /**
     * @dataProvider resultData
     *
     * @param $apiData
     * @param $formData
     */
    public function testMapFromResult($apiData, $formData): void
    {
        static::assertEquals(
            $formData,
            $this->communityLicence->mapFromResult($apiData)
        );
    }

    /**
     * @dataProvider  resultData
     * @param $apiData
     * @param $formData
     */
    public function testMapFromForm($apiData, $formData): void
    {
        $apiData['communityLicenceDocumentStatus'] = $apiData['communityLicenceDocumentStatus']['id'];

        static::assertEquals(
            $apiData,
            $this->communityLicence->mapFromForm($formData)
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
        return match ($id) {
            'doc_sts_destroyed' => 'possession',
            'doc_sts_lost' => 'lost',
            'doc_sts_stolen' => 'stolen',
            default => '',
        };
    }
}
