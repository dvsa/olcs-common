<?php


namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\Generic;
use Common\Service\Data\TransportManagerApplication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class TransportManagerApplicationTest
 * @package CommonTest\Service\Data
 */
class TransportManagerApplicationTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->sut = new TransportManagerApplication();
    }

    public function testFetchTmListOptionsByApplicationId()
    {
        $applicationId = 99;
        $mockData = [
            'Count' => 2,
            'Results' => [
                0 => [
                    'transportManager' => [
                        'id' => 1,
                        'homeCd' => [
                            'person' => [
                                'forename' => 'John',
                                'familyName' => 'Smith'
                            ]
                        ]
                    ]
                ],
                1 => [
                    'transportManager' => [
                        'id' => 2,
                        'homeCd' => [
                            'person' => [
                                'forename' => 'Ian',
                                'familyName' => 'Williams'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $mockResult = [
            1 => 'John Smith',
            2 => 'Ian Williams'
        ];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with(m::type('array'))->andReturn($mockData);

        $this->sut->setRestClient($mockClient);

        $this->assertEquals($mockResult, $this->sut->fetchTmListOptionsByApplicationId($applicationId));

        //check caching
        $this->assertEquals($mockResult, $this->sut->fetchTmListOptionsByApplicationId($applicationId));
    }

    public function testGetServiceName()
    {
        $this->assertEquals('TransportManagerApplication', $this->sut->getServiceName());
    }

    public function testGetBundle()
    {
        $this->assertInternalType('array', $this->sut->getBundle());
    }

    public function testSetId()
    {
        $this->sut->setId(78);
        $this->assertEquals(78, $this->sut->getId());
    }

    public function testGetId()
    {
        $this->assertNull($this->sut->getId());
    }
}
