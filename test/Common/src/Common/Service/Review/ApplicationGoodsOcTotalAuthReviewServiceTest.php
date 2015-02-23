<?php

/**
 * Application Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\ApplicationGoodsOcTotalAuthReviewService;

/**
 * Application Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsOcTotalAuthReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationGoodsOcTotalAuthReviewService();
    }

    public function testGetConfigFromData()
    {
        $data = [
            'totAuthVehicles' => 100,
            'totAuthTrailers' => 150,
            'licenceType' => [
                'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
            ]

        ];
        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 100
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 150
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithStandardInternational()
    {
        $data = [
            'totAuthVehicles' => 100,
            'totAuthTrailers' => 150,
            'totCommunityLicences' => 200,
            'licenceType' => [
                'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            ]

        ];
        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 100
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 150
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-community-licences',
                        'value' => 200
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
