<?php

/**
 * Bus Processing Service Test
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;
use Common\Service\Processing\BusProcessingService;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Entity\LicenceEntityService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Bus Processing Service Test
 */
class BusProcessingServiceTest extends MockeryTestCase
{
    use MockDateTrait;

    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new BusProcessingService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testMaybeCreateFeeNoExistingFee()
    {
        $busRegId = 123;

        $this->sut = m::mock('Common\Service\Processing\BusProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestFeeForBusReg')
                ->once()
                ->with($busRegId)
                ->andReturn(null)
                ->getMock()
        );

        $this->sut->shouldReceive('createFee')->once()->with($busRegId);

        $this->sut->maybeCreateFee($busRegId);
    }

    public function testMaybeCreateFeeWithExistingFee()
    {
        $busRegId = 123;

        $this->sut = m::mock('Common\Service\Processing\BusProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestFeeForBusReg')
                ->once()
                ->with($busRegId)
                ->andReturn(['id' => 99, 'amount' => '99.99'])
                ->getMock()
        );

        $this->sut->shouldReceive('createFee')->never();

        $this->sut->maybeCreateFee($busRegId);
    }

    public function testMaybeCreateFeeWithoutBusRegId()
    {
        $busRegId = null;

        $this->sut = m::mock('Common\Service\Processing\BusProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestFeeForBusReg')
                ->never()
                ->getMock()
        );

        $this->sut->shouldReceive('createFee')->never();

        $this->sut->maybeCreateFee($busRegId);
    }

    /**
     * @dataProvider createFeeProvider
     *
     * @param $busRegId
     * @param $busRegData
     * @param $feeTypeData
     * @param $expectedFeeType
     * @param $expectedTrafficArea
     * @param $expectedData
     */
    public function testCreateFee($busRegId, $busRegData, $feeTypeData, $expectedFeeType, $expectedTrafficArea, $expectedData)
    {
        $this->sut = m::mock('Common\Service\Processing\BusProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\BusReg',
            m::mock()
                ->shouldReceive('getDataForFees')
                ->once()
                ->with($busRegId)
                ->andReturn($busRegData)
                ->getMock()
        );

        $this->sm->setService(
            'Data\FeeType',
            m::mock()
                ->shouldReceive('getLatest')
                ->once()
                ->with(
                    $expectedFeeType,
                    LicenceEntityService::LICENCE_CATEGORY_PSV,
                    $busRegData['licence']['licenceType']['id'],
                    $busRegData['receivedDate'],
                    $expectedTrafficArea
                )
                ->andReturn($feeTypeData)
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('save')
                ->once()
                ->with($expectedData)
                ->andReturn(true)
                ->getMock()
        );

        $this->sut->createFee($busRegId);
    }


    /**
     * testCreateFee Data Provider
     *
     * @return array
     */
    public function createFeeProvider()
    {
        return [
            // BUSAPP - Scotland
            [
                // busRegId
                1,
                // busRegData
                [
                    'regNo' => 'regNo',
                    'variationNo' => 0,
                    'receivedDate' => 'date',
                    'licence' => [
                        'id' => 987,
                        'licenceType' => [
                            'id' => 123
                        ],
                        'trafficArea' => [
                            'id' => 111,
                            'isScotland' => 1
                        ]
                    ]
                ],
                // feeTypeData
                [
                    'id' => 11,
                    'fixedValue' => 10.00,
                    'description' => 'BUSAPP - Scotland'
                ],
                // expectedFeeType
                FeeTypeDataService::FEE_TYPE_BUSAPP,
                // expectedTrafficArea
                111,
                // expectedData
                [
                    'amount' => 10,
                    'busReg' => 1,
                    'licence' => 987,
                    'invoicedDate' => 'date',
                    'feeType' => 11,
                    'description' => 'BUSAPP - Scotland regNo Variation 0',
                    'feeStatus' => FeeEntityService::STATUS_OUTSTANDING
                ]
            ],
            // BUSAPP - Other
            [
                // busRegId
                2,
                // busRegData
                [
                    'regNo' => 'regNo',
                    'variationNo' => 0,
                    'receivedDate' => 'date',
                    'licence' => [
                        'id' => 987,
                        'licenceType' => [
                            'id' => 123
                        ],
                        'trafficArea' => [
                            'id' => 222,
                            'isScotland' => 0
                        ]
                    ]
                ],
                // feeTypeData
                [
                    'id' => 22,
                    'fixedValue' => 10.00,
                    'description' => 'BUSAPP - Other'
                ],
                // expectedFeeType
                FeeTypeDataService::FEE_TYPE_BUSAPP,
                // expectedTrafficArea
                null,
                // expectedData
                [
                    'amount' => 10,
                    'busReg' => 2,
                    'licence' => 987,
                    'invoicedDate' => 'date',
                    'feeType' => 22,
                    'description' => 'BUSAPP - Other regNo Variation 0',
                    'feeStatus' => FeeEntityService::STATUS_OUTSTANDING
                ]
            ],
            // BUSVAR - Scotland
            [
                // busRegId
                3,
                // busRegData
                [
                    'regNo' => 'regNo',
                    'variationNo' => 1,
                    'receivedDate' => 'date',
                    'licence' => [
                        'id' => 987,
                        'licenceType' => [
                            'id' => 123
                        ],
                        'trafficArea' => [
                            'id' => 333,
                            'isScotland' => 1
                        ]
                    ]
                ],
                // feeTypeData
                [
                    'id' => 33,
                    'fixedValue' => 10.00,
                    'description' => 'BUSVAR - Scotland'
                ],
                // expectedFeeType
                FeeTypeDataService::FEE_TYPE_BUSVAR,
                // expectedTrafficArea
                333,
                // expectedData
                [
                    'amount' => 10,
                    'busReg' => 3,
                    'licence' => 987,
                    'invoicedDate' => 'date',
                    'feeType' => 33,
                    'description' => 'BUSVAR - Scotland regNo Variation 1',
                    'feeStatus' => FeeEntityService::STATUS_OUTSTANDING
                ]
            ],
            // BUSVAR - Other
            [
                // busRegId
                4,
                // busRegData
                [
                    'regNo' => 'regNo',
                    'variationNo' => 2,
                    'receivedDate' => 'date',
                    'licence' => [
                        'id' => 987,
                        'licenceType' => [
                            'id' => 123
                        ],
                        'trafficArea' => [
                            'id' => 444,
                            'isScotland' => 0
                        ]
                    ]
                ],
                // feeTypeData
                [
                    'id' => 44,
                    'fixedValue' => 10.00,
                    'description' => 'BUSVAR - Other'
                ],
                // expectedFeeType
                FeeTypeDataService::FEE_TYPE_BUSVAR,
                // expectedTrafficArea
                null,
                // expectedData
                [
                    'amount' => 10,
                    'busReg' => 4,
                    'licence' => 987,
                    'invoicedDate' => 'date',
                    'feeType' => 44,
                    'description' => 'BUSVAR - Other regNo Variation 2',
                    'feeStatus' => FeeEntityService::STATUS_OUTSTANDING
                ]
            ],
        ];
    }
}
