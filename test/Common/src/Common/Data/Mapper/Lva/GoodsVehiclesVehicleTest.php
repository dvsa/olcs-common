<?php

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\GoodsVehiclesVehicle;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Form\FormInterface;

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsVehiclesVehicleTest extends MockeryTestCase
{
    public function testMapFromResult()
    {
        $input = [
            'bar' => 'foo',
            'version' => 1,
            'vehicle' => [
                'foo' => 'bar'
            ],
            'goodsDiscs' => [
                [
                    'discNo' => 1234
                ]
            ]
        ];

        $output = GoodsVehiclesVehicle::mapFromResult($input);

        $expected = [
            'licence-vehicle' => [
                'bar' => 'foo',
                'version' => 1,
                'discNo' => 'Pending'
            ],
            'data' => [
                'foo' => 'bar',
                'version' => 1
            ]
        ];

        $this->assertEquals($expected, $output);
    }

    public function testMapFromErrors()
    {
        $errors = [
            'vrm' => [
                'Error1'
            ],
            'receivedDate' => [
                'Error2'
            ],
            'global' => [
                'Error3'
            ]
        ];
        $formMessages = [
            'data' => [
                'vrm' => ['Error1']
            ],
            'licenceVehicle' => [
                'receivedDate' => ['Error2']
            ]
        ];
        $expected = [
            'global' => [
                'Error3'
            ]
        ];

        /** @var FormInterface $mockForm */
        $mockForm = m::mock(FormInterface::class)
            ->shouldReceive('setMessages')
            ->with($formMessages)
            ->once()
            ->getMock();

        static::assertEquals($expected, GoodsVehiclesVehicle::mapFromErrors($errors, $mockForm));
    }
}
