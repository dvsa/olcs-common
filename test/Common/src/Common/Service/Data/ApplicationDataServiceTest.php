<?php

namespace OlcsTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\Application;
use Common\Service\Entity\ApplicationEntityService;
use Mockery as m;

/**
 * Class Application Data Service Test
 * @package CommonTest\Service
 */
class ApplicationDataServiceTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\Application
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->sut = new Application();
    }

    public function testGetServiceName()
    {
        $this->assertEquals('Application', $this->sut->getServiceName());
    }

    public function testFetchData()
    {
        $id = 1;
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockData = ['id' => 99];

        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('/' . $id, m::type('array'))
            ->andReturn($mockData);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->fetchData($id);

        $this->assertEquals($mockData, $result);
    }

    /**
     * Test canHaveCases
     *
     * @dataProvider canHaveCasesDataProvider
     * @param array $application
     * @param int $expectedCallsNo
     */
    public function testCanHaveCases($application, $expectedCallsNo)
    {
        $this->sut->setData($application['id'], $application);
        $this->sut->canHaveCases($application['id']);
    }

    /**
     * Data provider for canHaveCases.
     *
     * @return array
     */
    public function canHaveCasesDataProvider()
    {
        return [
            // status / licence not set
            [
                ['id' => 100],
                1
            ],
            // licence without licNo
            [
                [
                    'id' => 100,
                    'licence' => ['licNo' => null]
                ],
                1
            ],
            // status NOT_SUBMITTED
            [
                [
                    'id' => 100,
                    'status' => ['id' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED]
                ],
                1
            ],
            // licence with licNo and status different than NOT_SUBMITTED
            [
                [
                    'id' => 100,
                    'status' => ['id' => ApplicationEntityService::APPLICATION_STATUS_GRANTED],
                    'licence' => ['licNo' => 'ABC']
                ],
                0
            ],
        ];
    }
}
