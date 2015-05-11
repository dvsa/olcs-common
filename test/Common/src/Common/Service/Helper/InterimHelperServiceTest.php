<?php

namespace CommonTest\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use CommonTest\Bootstrap;
use Common\Service\Helper\InterimHelperService;
use Common\Service\Entity\CommunityLicEntityService;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Data\CategoryDataService as Category;
use Common\Service\Printing\PrintSchedulerInterface;

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
                array('hasAuthVehiclesIncrease' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 11,
                    'licence' => array(
                        'totAuthTrailers' => 10
                    )
                ),
                array('hasAuthTrailersIncrease' => 'totAuthTrailers')
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
                array('hasAuthVehiclesIncrease' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 10,
                    'licence' => array(
                        'totAuthTrailers' => 10
                    )
                ),
                array('hasAuthTrailersIncrease' => 'totAuthTrailers')
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

    public function interimGrantingProvider()
    {
        return [
            [false, 'NEW_APP_INT_GRANTED'],
            [true, 'VAR_APP_INT_GRANTED']
        ];
    }

    /**
     * @group interimHelper
     * @dataProvider interimGrantingProvider
     */
    public function testInterimGranting($variationFlag, $templateName)
    {
        $applicationId = 10;
        $licenceId = 2;

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
                    ],
                    'interimApplication' => ['foo' => 'bar']
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
            ->shouldReceive('getDataForProcessing')
            ->with($applicationId)
            ->andReturn(['licence' => ['id' => $licenceId], 'isVariation' => $variationFlag])
            ->once()
            ->getMock()
        );

        $this->mockInterimLetterGeneration($templateName, $applicationId, $licenceId);

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
            ->with(99, [50], $applicationId)
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

    protected function mockInterimLetterGeneration($templateName, $applicationId, $licenceId)
    {
        $this->sm->setService(
            'Entity\User',
            m::mock()
            ->shouldReceive('getCurrentUser')
            ->andReturn(['id' => 2])
            ->getMock()
        );

        $this->sm->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateAndStore')
            ->with(
                $templateName,
                $templateName,
                [
                    'application' => $applicationId,
                    'licence' => $licenceId,
                    'user' => 2
                ]
            )
            ->andReturn('storedFile')
            ->getMock()
        );

        $this->sm->setService(
            'Helper\DocumentDispatch',
            m::mock()
            ->shouldReceive('process')
            ->with(
                'storedFile',
                [
                    'description'   => $templateName,
                    'filename'      => str_replace(" ", "_", $templateName) . '.rtf',
                    'application'   => $applicationId,
                    'licence'       => $licenceId,
                    'category'      => Category::CATEGORY_LICENSING,
                    'subCategory'   => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                    'isExternal'     => false,
                    'isScan'        => false
                ]
            )
            ->getMock()
        );
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
                    'goodsDiscs' => [],
                    'interimApplication' => null
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
            ->shouldReceive('getDataForProcessing')
            ->with($applicationId)
            ->andReturn(['licence' => ['id' => 99], 'isVariation' => false])
            ->once()
            ->getMock()
        );

        $this->mockInterimLetterGeneration('NEW_APP_INT_GRANTED', $applicationId, 99);

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
            ->with(99, [50], $applicationId)
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
                    ],
                    'interimApplication' => ['foo' => 'bar']
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
            ->shouldReceive('getDataForProcessing')
            ->with($applicationId)
            ->andReturn(['licence' => ['id' => 99], 'isVariation' => false])
            ->once()
            ->getMock()
        );

        $this->mockInterimLetterGeneration('NEW_APP_INT_GRANTED', $applicationId, 99);

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
            ->shouldReceive('getDataForProcessing')
            ->with($applicationId)
            ->andReturn(['licence' => ['id' => 99], 'isVariation' => false])
            ->once()
            ->getMock()
        );

        $this->mockInterimLetterGeneration('NEW_APP_INT_GRANTED', $applicationId, 99);

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
            ->with(99, [50], $applicationId)
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

    public function testVoidDiscsForApplication()
    {
        $applicationId = 69;

        $interimData = [
            'id' => $applicationId,
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
                ],
                [
                    'id' => 21,
                    'version' => 200,
                    'goodsDiscs' => [
                        [
                            'ceasedDate' => null,
                            'id' => 41,
                            'version' => 401
                        ]
                    ]
                ]
            ],
        ];
        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForInterim')
                ->with($applicationId)
                ->andReturn($interimData)
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
                            'ceasedDate' => '2015-03-18 00:00:00',
                        ],
                        [
                            'id' => 41,
                            'version' => 401,
                            'ceasedDate' => '2015-03-18 00:00:00',
                        ]
                    ]
                )
                ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
                ->shouldReceive('getDate')
                ->andReturn('2015-03-18 00:00:00')
                ->getMock()
        );

        $this->sut->voidDiscsForApplication($applicationId);
    }

    /**
     * @group interimHelper
     */
    public function testInterimRefusing()
    {
        $applicationId = 10;

        $interimData = [
            'id' => $applicationId,
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
            ],
            'isVariation' => 0,
            'niFlag' => 'N'
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
                    'id' => 10,
                    'version' => 100,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_REFUSED,
                    'interimEnd' => '2015-01-01 10:10:10'
                ]
            )
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01 10:10:10')
            ->getMock()
        );

        $this->sm->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateAndStore')
            ->with('GB/NEW_APP_INT_REFUSED', 'GV Refused Interim Licence', ['user' => 1, 'licence' => 99])
            ->andReturn('file')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\User',
            m::mock()
            ->shouldReceive('getCurrentUser')
            ->andReturn(['id' => 1])
            ->getMock()
        );

        $dataToSave = [
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'description' => 'GV Refused Interim Licence',
            'filename' => 'GV Refused Interim Licence.rtf',
            'issuedDate' => '2015-01-01 10:10:10',
            'isExternal' => false,
            'isScan' => false,
            'licence' => 99,
            'application' => $applicationId
        ];

        $this->sm->setService(
            'Helper\DocumentDispatch',
            m::mock()
            ->shouldReceive('process')
            ->with('file', $dataToSave)
            ->andReturn('file')
            ->getMock()
        );

        $this->assertEquals('file', $this->sut->refuseInterim($applicationId));
    }
}
