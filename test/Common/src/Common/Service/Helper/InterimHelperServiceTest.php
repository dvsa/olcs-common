<?php

namespace CommonTest\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use CommonTest\Bootstrap;
use Common\Service\Helper\InterimHelperService;
use Common\Service\Entity\CommunityLicEntityService;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Class InterimHelperServiceTest
 *
 * Test the interim helper service view determining logical methods.
 *
 * @package CommonTest\Service\Helper
 */
class InterimHelperServiceTest extends MockeryTestCase
{
    protected $sut = null;
    protected $sm = null;

    public function setUp()
    {
        $this->sut = new InterimHelperService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function canVariationInterimTrueProvider()
    {
        return array(
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_sn'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_sn'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'totAuthVehicles' => 11,
                    'licence' => array(
                        'totAuthVehicles' => 10
                    )
                ),
                array('hasAuthIncrease' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 11,
                    'licence' => array(
                        'totAuthTrailers' => 10
                    )
                ),
                array('hasAuthIncrease' => 'totAuthTrailers')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'A'
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array()
                    )
                ),
                array('hasNewOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'U',
                            'noOfVehiclesRequired' => 11,
                            'noOfTrailersRequired' => 10,
                            'operatingCentre' => array(
                                'id' => 1
                            )
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10,
                                'operatingCentre' => array(
                                    'id' => 1
                                )
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'U',
                            'noOfVehiclesRequired' => 10,
                            'noOfTrailersRequired' => 11,
                            'operatingCentre' => array(
                                'id' => 1
                            )
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10,
                                'operatingCentre' => array(
                                    'id' => 1
                                )
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'U',
                            'noOfVehiclesRequired' => 10,
                            'noOfTrailersRequired' => 11,
                            'operatingCentre' => array(
                                'id' => 1
                            )
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10,
                                'operatingCentre' => array(
                                    'id' => 1
                                )
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            )
        );
    }

    public function canVariationInterimFalseProvider()
    {
        return array(
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sr'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'totAuthVehicles' => 10,
                    'licence' => array(
                        'totAuthVehicles' => 10
                    )
                ),
                array('hasAuthIncrease' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 10,
                    'licence' => array(
                        'totAuthTrailers' => 10
                    )
                ),
                array('hasAuthIncrease' => 'totAuthTrailers')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'U'
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array()
                    )
                ),
                array('hasNewOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(),
                    'licence' => array(
                        'operatingCentres' => array()
                    )
                ),
                array('hasNewOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'action' => 'D',
                            'noOfVehiclesRequired' => 10,
                            'noOfTrailersRequired' => 11,
                            'operatingCentre' => array(
                                'id' => 1
                            )
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10,
                                'operatingCentre' => array(
                                    'id' => 1
                                )
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10,
                                'operatingCentre' => array(
                                    'id' => 1
                                )
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            )
        );
    }

    /**
     * @dataProvider canVariationInterimTrueProvider
     */
    public function testCanVariationTrueInterim($interimData, $functionAndKey)
    {
        $applicationId = 123;

        $this->sut->setFunctionToDataMap($functionAndKey);

        $this->sm->shouldReceive('get')
            ->with('Entity/Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVariationInterimData')
                    ->andReturn($interimData)
                    ->getMock()
            );

        $this->assertTrue($this->sut->canVariationInterim($applicationId));
    }

    /**
     * @dataProvider canVariationInterimFalseProvider
     */
    public function testCanVariationFalseInterim($interimData, $functionAndKey)
    {
        $applicationId = 123;

        $this->sut->setFunctionToDataMap($functionAndKey);

        $this->sm->shouldReceive('get')
            ->with('Entity/Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVariationInterimData')
                    ->andReturn($interimData)
                    ->getMock()
            );

        $this->assertFalse($this->sut->canVariationInterim($applicationId));
    }

    public function testCreateInterimFeeIfNotExist()
    {
        $applicationId = 123;

        $this->sm->shouldReceive('get')
            ->with('Processing\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeTypeForApplication')
                    ->with(123, 'GRANTINT')
                    ->shouldReceive('createFee')
                    ->with(123, null, 'GRANTINT')
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Entity\Fee')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeByTypeStatusesAndApplicationId')
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Entity\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDataForInterim')
                    ->getMock()
            );

        $this->sut->createInterimFeeIfNotExist($applicationId);
    }

    public function testCancelInterimFees()
    {
        $applicationId = 123;

        $this->sm->shouldReceive('get')
            ->with('Entity\Fee')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeByTypeStatusesAndApplicationId')
                    ->andReturn(
                        array(
                            array('id' => 1)
                        )
                    )
                    ->shouldReceive('cancelByIds')
                    ->with(array(1))
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Processing\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeTypeForApplication')
                    ->with(123, 'GRANTINT')
                    ->getMock()
            );

        $this->sut->cancelInterimFees($applicationId);
    }

    public function testCanVariationInterimInvalidProvider()
    {
        return array(
            array(null),
            array("string")
        );
    }

    /**
     * @dataProvider testCanVariationInterimInvalidProvider
     */
    public function testCanVariationInterimThrowsExpection($applicationId)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->canVariationInterim($applicationId);
    }

    /**
     * @group interimHelper
     */
    public function testInterimGranting()
    {
        $applicationId = 10;

        $interimData = [
            'id' => 10,
            'version' => 100,
            'licenceVehicles' => [
                [
                    'id' => 20,
                    'version' => 200,
                    'goodsDiscs' => [
                        [
                            'ceasedDate' => null,
                            'id' => 40,
                            'version' => 400
                        ]
                    ]
                ]
            ],
            'licence' => [
                'communityLics' => [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => [
                            'id' => CommunityLicEntityService::STATUS_PENDING
                        ]
                    ]
                ],
                'id' => 99
            ]
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->with($applicationId)
            ->andReturn($interimData)
            ->shouldReceive('save')
            ->with(
                [
                    'id' => $applicationId,
                    'version' => 100,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_INFORCE
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2014-01-01 00:00:00')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\LicenceVehicle',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 20,
                        'version' => 200,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\GoodsDisc',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 40,
                        'version' => 400,
                        'ceasedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->shouldReceive('save')
            ->with(
                [
                    [
                        'licenceVehicle' => 20,
                        'isInterim' => 'Y'
                    ],
                    '_OPTIONS_' => [
                        'multiple' => true
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\CommunityLic',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => CommunityLicEntityService::STATUS_ACTIVE,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with(99, [50])
            ->getMock()
        );

        $this->sm->setService(
            'Processing\Licence',
            m::mock()
            ->shouldReceive('generateInterimDocument')
            ->getMock()
        );

        $this->assertEquals(null, $this->sut->grantInterim($applicationId));
    }

    /**
     * @group interimHelper
     */
    public function testInterimGrantingNoDiscsVoiding()
    {
        $applicationId = 10;

        $interimData = [
            'id' => 10,
            'version' => 100,
            'licenceVehicles' => [
                [
                    'id' => 20,
                    'version' => 200,
                    'goodsDiscs' => []
                ]
            ],
            'licence' => [
                'communityLics' => [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => [
                            'id' => CommunityLicEntityService::STATUS_PENDING
                        ]
                    ]
                ],
                'id' => 99
            ]
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->with($applicationId)
            ->andReturn($interimData)
            ->shouldReceive('save')
            ->with(
                [
                    'id' => $applicationId,
                    'version' => 100,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_INFORCE
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\GoodsDisc',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    [
                        'licenceVehicle' => 20,
                        'isInterim' => 'Y'
                    ],
                    '_OPTIONS_' => [
                        'multiple' => true
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2014-01-01 00:00:00')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\LicenceVehicle',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 20,
                        'version' => 200,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\CommunityLic',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => CommunityLicEntityService::STATUS_ACTIVE,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with(99, [50])
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Processing\Licence',
            m::mock()
                ->shouldReceive('generateInterimDocument')
                ->getMock()
        );

        $this->assertEquals(null, $this->sut->grantInterim($applicationId));
    }

    /**
     * @group interimHelper
     */
    public function testInterimGrantingNoCommunityLicencesProcessed()
    {
        $applicationId = 10;

        $interimData = [
            'id' => 10,
            'version' => 100,
            'licenceVehicles' => [
                [
                    'id' => 20,
                    'version' => 200,
                    'goodsDiscs' => [
                        [
                            'ceasedDate' => null,
                            'id' => 40,
                            'version' => 400
                        ]
                    ]
                ]
            ],
            'licence' => [
                'communityLics' => [],
                'id' => 99
            ]
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->with($applicationId)
            ->andReturn($interimData)
            ->shouldReceive('save')
            ->with(
                [
                    'id' => $applicationId,
                    'version' => 100,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_INFORCE
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2014-01-01 00:00:00')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\LicenceVehicle',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 20,
                        'version' => 200,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\GoodsDisc',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 40,
                        'version' => 400,
                        'ceasedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->shouldReceive('save')
            ->with(
                [
                    [
                        'licenceVehicle' => 20,
                        'isInterim' => 'Y'
                    ],
                    '_OPTIONS_' => [
                        'multiple' => true
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Processing\Licence',
            m::mock()
                ->shouldReceive('generateInterimDocument')
                ->getMock()
        );

        $this->assertEquals(null, $this->sut->grantInterim($applicationId));
    }

    /**
     * @group interimHelper
     */
    public function testInterimGrantingNoLicenceVehicles()
    {
        $applicationId = 10;

        $interimData = [
            'id' => 10,
            'version' => 100,
            'licenceVehicles' => [],
            'licence' => [
                'communityLics' => [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => [
                            'id' => CommunityLicEntityService::STATUS_PENDING
                        ]
                    ]
                ],
                'id' => 99
            ]
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->with($applicationId)
            ->andReturn($interimData)
            ->shouldReceive('save')
            ->with(
                [
                    'id' => $applicationId,
                    'version' => 100,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_INFORCE
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2014-01-01 00:00:00')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\CommunityLic',
            m::mock()
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'id' => 50,
                        'version' => 500,
                        'status' => CommunityLicEntityService::STATUS_ACTIVE,
                        'specifiedDate' => '2014-01-01 00:00:00'
                    ]
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with(99, [50])
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Processing\Licence',
            m::mock()
                ->shouldReceive('generateInterimDocument')
                ->getMock()
        );

        $this->assertEquals(null, $this->sut->grantInterim($applicationId));
    }
}
