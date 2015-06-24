<?php

/**
 * Transfer Vehicles Business Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TransferVehicles;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\BusinessService\Response;

/**
 * Transfer Vehicles Business Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransferVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new TransferVehicles();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @group transferVehiclesBusinessService
     */
    public function testProcessPsv()
    {
        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $params = [
            'id' => '1,2',
            'sourceLicenceId' => $sourceLicenceId,
            'targetLicenceId' => $targetLicenceId
        ];
        $targetLicence = [
            'licenceVehicles' => [
                [
                    'vehicle' => ['id' => 1, 'vrm' => 'vrm1'],
                    'goodsDiscs' => [['id' => 11], ['id' => 12]]
                ],
                [
                    'vehicle' => ['id' => 2, 'vrm' => 'vrm2'],
                    'goodsDiscs' => [['id' => 21], ['id' => 22]]
                ],
            ],
            'licNo' => '123',
            'totAuthVehicles' => 10,
            'goodsOrPsv' => ['id' => LicenceEntityService::LICENCE_CATEGORY_PSV]
        ];
        $ids = [1, 2];
        $sourceVehiclesIds = [3,4];
        $targetLicenceVehiclesIds = ['id' => [5, 6]];

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getLicenceWithVehicles')
            ->with($targetLicenceId)
            ->andReturn($targetLicence)
            ->once()
            ->shouldReceive('getVehiclesIdsByLicenceVehiclesIds')
            ->with($sourceLicenceId, $ids)
            ->andReturn($sourceVehiclesIds)
            ->once()
            ->getMock()
        );

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->with('Y-m-d H:i:s')
            ->andReturn('2015-01-01')
            ->getMock()
        );
        $this->sm->setService(
            'Entity\User',
            m::mock()
            ->shouldReceive('getCurrentUser')
            ->andReturn(['id' => 999])
            ->getMock()
        );

        $dataToCreate = [
            [
                'vehicle' => 3,
                'licence' => $targetLicenceId,
                'specifiedDate' => '2015-01-01',
                'createdBy' => 999,
                'lastModifiedBy' => 999
            ],
            [
                'vehicle' => 4,
                'licence' => $targetLicenceId,
                'specifiedDate' => '2015-01-01',
                'createdBy' => 999,
                'lastModifiedBy' => 999
            ]
        ];
        $this->sm->setService(
            'Entity\LicenceVehicle',
            m::mock()
            ->shouldReceive('removeVehicles')
            ->with($ids)
            ->once()
            ->shouldReceive('multiCreate')
            ->with($dataToCreate)
            ->andReturn($targetLicenceVehiclesIds)
            ->once()
            ->getMock()
        );

        $this->assertInstanceOf('Common\BusinessService\Response', $this->sut->process($params));
    }

    /**
     * @group transferVehiclesBusinessService
     */
    public function testProcessGoods()
    {
        $this->markTestSkipped();

        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $params = [
            'id' => '1,2',
            'sourceLicenceId' => $sourceLicenceId,
            'targetLicenceId' => $targetLicenceId
        ];
        $targetLicence = [
            'licenceVehicles' => [
                [
                    'vehicle' => ['id' => 1, 'vrm' => 'vrm1'],
                    'goodsDiscs' => [['id' => 11], ['id' => 12]]
                ],
                [
                    'vehicle' => ['id' => 2, 'vrm' => 'vrm2'],
                    'goodsDiscs' => [['id' => 21], ['id' => 22]]
                ],
            ],
            'licNo' => '123',
            'totAuthVehicles' => 10,
            'goodsOrPsv' => ['id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE]
        ];
        $ids = [1, 2];
        $targetLicenceVehiclesIds = ['id' => [5, 6]];
        $discsToCreate = [['id' => 5], ['id' => 6]];

        $sourceVehiclesIds = [3,4];
        $sourceLicence = [
            'licenceVehicles' => [
                [
                    'id' => 1,
                    'goodsDiscs' => [['id' => 33], ['id' => 34]]
                ],
                [
                    'id' => 2,
                    'goodsDiscs' => [['id' => 43], ['id' => 44]]
                ]
            ]
        ];
        $discsToCease = [33, 34, 43, 44];

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getLicenceWithVehicles')
            ->with($targetLicenceId)
            ->andReturn($targetLicence)
            ->once()
            ->shouldReceive('getLicenceWithVehicles')
            ->with($sourceLicenceId)
            ->andReturn($sourceLicence)
            ->once()
            ->shouldReceive('getVehiclesIdsByLicenceVehiclesIds')
            ->with($sourceLicenceId, $ids)
            ->andReturn($sourceVehiclesIds)
            ->once()
            ->getMock()
        );

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->with('Y-m-d H:i:s')
            ->andReturn('2015-01-01')
            ->getMock()
        );
        $this->sm->setService(
            'Entity\User',
            m::mock()
            ->shouldReceive('getCurrentUser')
            ->andReturn(['id' => 999])
            ->getMock()
        );

        $dataToCreate = [
            [
                'vehicle' => 3,
                'licence' => $targetLicenceId,
                'specifiedDate' => '2015-01-01',
                'createdBy' => 999,
                'lastModifiedBy' => 999
            ],
            [
                'vehicle' => 4,
                'licence' => $targetLicenceId,
                'specifiedDate' => '2015-01-01',
                'createdBy' => 999,
                'lastModifiedBy' => 999
            ]
        ];
        $this->sm->setService(
            'Entity\LicenceVehicle',
            m::mock()
            ->shouldReceive('removeVehicles')
            ->with($ids)
            ->once()
            ->shouldReceive('multiCreate')
            ->with($dataToCreate)
            ->andReturn($targetLicenceVehiclesIds)
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Entity\GoodsDisc',
            m::mock()
            ->shouldReceive('ceaseDiscs')
            ->with($discsToCease)
            ->once()
            ->shouldReceive('createForVehicles')
            ->with($discsToCreate)
            ->once()
            ->getMock()
        );

        $this->assertInstanceOf('Common\BusinessService\Response', $this->sut->process($params));
    }

    /**
     * @group transferVehiclesBusinessService
     */
    public function testProcessWrongAuthority()
    {
        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $params = [
            'id' => '1,2',
            'sourceLicenceId' => $sourceLicenceId,
            'targetLicenceId' => $targetLicenceId
        ];
        $targetLicence = [
            'licenceVehicles' => [
                [
                    'vehicle' => ['id' => 1, 'vrm' => 'vrm1'],
                    'goodsDiscs' => [['id' => 11], ['id' => 12]]
                ],
                [
                    'vehicle' => ['id' => 2, 'vrm' => 'vrm2'],
                    'goodsDiscs' => [['id' => 21], ['id' => 22]]
                ],
            ],
            'licNo' => '123',
            'totAuthVehicles' => 2,
            'goodsOrPsv' => ['id' => LicenceEntityService::LICENCE_CATEGORY_PSV]
        ];

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getLicenceWithVehicles')
            ->with($targetLicenceId)
            ->andReturn($targetLicence)
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Translation',
            m::mock()
            ->shouldReceive('translate')
            ->with('licence.vehicles_transfer.form.message_exceed')
            ->andReturn('authority_error %s')
            ->once()
            ->getMock()
        );

        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals('authority_error 123', $response->getMessage());
        $this->assertInstanceOf('Common\BusinessService\Response', $response);
    }

    /**
     * @group transferVehiclesBusinessService
     */
    public function testProcessVehiclesExists()
    {
        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $params = [
            'id' => '1,2',
            'sourceLicenceId' => $sourceLicenceId,
            'targetLicenceId' => $targetLicenceId
        ];
        $targetLicence = [
            'licenceVehicles' => [
                [
                    'vehicle' => ['id' => 3, 'vrm' => 'vrm1'],
                    'goodsDiscs' => [['id' => 11], ['id' => 12]]
                ],
                [
                    'vehicle' => ['id' => 4, 'vrm' => 'vrm2'],
                    'goodsDiscs' => [['id' => 21], ['id' => 22]]
                ],
            ],
            'licNo' => '123',
            'totAuthVehicles' => 10,
            'goodsOrPsv' => ['id' => LicenceEntityService::LICENCE_CATEGORY_PSV]
        ];
        $ids = [1, 2];
        $sourceVehiclesIds = ['vrm1' => 3, 'vrm2' => 4];

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getLicenceWithVehicles')
            ->with($targetLicenceId)
            ->andReturn($targetLicence)
            ->once()
            ->shouldReceive('getVehiclesIdsByLicenceVehiclesIds')
            ->with($sourceLicenceId, $ids)
            ->andReturn($sourceVehiclesIds)
            ->once()
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Translation',
            m::mock()
            ->shouldReceive('translate')
            ->with('licence.vehicles_transfer.form.message_already_on_licence')
            ->andReturn('vehicles_exists_error %s for %s')
            ->once()
            ->getMock()
        );

        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals('vehicles_exists_error vrm1, vrm2 for 123', $response->getMessage());
        $this->assertInstanceOf('Common\BusinessService\Response', $response);
    }
}
