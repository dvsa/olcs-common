<?php

/**
 * Goods Disc Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\GoodsDiscEntityService;

/**
 * Goods Disc Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GoodsDiscEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new GoodsDiscEntityService();

        parent::setUp();
    }

    public function testCeaseDiscs()
    {
        $ids = array(3, 7, 8);

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = [
            [
                'id' => 3,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            [
                'id' => 7,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            [
                'id' => 8,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectOneRestCall('GoodsDisc', 'PUT', $data);

        $this->sut->ceaseDiscs($ids);
    }

    public function testCreateForVehicles()
    {
        $vehicles = [
            ['id' => 2],
            ['id' => 5]
        ];

        $data = [
            [
                'licenceVehicle' => 2,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N',
                '_OPTIONS_' => ['force' => true]
            ],
            [
                'licenceVehicle' => 5,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N',
                '_OPTIONS_' => ['force' => true]
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectOneRestCall('GoodsDisc', 'POST', $data);

        $this->sut->createForVehicles($vehicles);
    }

    public function testUpdateExistingForLicenceWithActiveDiscs()
    {
        $vehicleData = [
            [
                'id' => 50,
                'goodsDiscs' => [
                    ['id' => 1, 'ceasedDate' => null],
                    ['id' => 3, 'ceasedDate' => '2010-10-10']
                ]
            ], [
                'id' => 60,
                'goodsDiscs' => [
                    ['id' => 5, 'ceasedDate' => null]
                ]
            ]
        ];

        $lvService = $this->getMock('\stdClass', ['getExistingForLicence']);
        $lvService->expects($this->once())
            ->method('getExistingForLicence')
            ->with(10, 20)
            ->willReturn($vehicleData);

        $this->sm->setService('Entity\LicenceVehicle', $lvService);

        $date = date('Y-m-d');

        $this->mockDate($date);

        $ceasedData = [
            [
                'id' => 1,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            [
                'id' => 5,
                'ceasedDate' => $date,
                '_OPTIONS_' => ['force' => true]
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectedRestCallInOrder('GoodsDisc', 'PUT', $ceasedData);

        $createData = [
            [
                'licenceVehicle' => 50,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N',
                '_OPTIONS_' => ['force' => true]
            ],
            [
                'licenceVehicle' => 60,
                'ceasedDate' => null,
                'issuedDate' => null,
                'discNo' => null,
                'isCopy' => 'N',
                '_OPTIONS_' => ['force' => true]
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectedRestCallInOrder('GoodsDisc', 'POST', $createData);

        $this->sut->updateExistingForLicence(10, 20);
    }
}
