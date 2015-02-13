<?php

/**
 * Grant Transport Manager Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\GrantTransportManagerProcessingService;

/**
 * Grant Transport Manager Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantTransportManagerProcessingServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new GrantTransportManagerProcessingService();
        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGrantWithoutData()
    {
        // Params
        $id = 123;
        $licenceId = 321;

        // Data
        $stubbedData = [];

        // Mocks
        $mockTma = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);

        // Expecations
        $mockTma->shouldReceive('getGrantDataForApplication')
            ->with($id)
            ->andReturn($stubbedData);

        $this->assertNull($this->sut->grant($id, $licenceId));
    }

    public function testGrantWithCreateWithExistingTmOnLicence()
    {
        // Params
        $id = 123;
        $licenceId = 321;

        // Data
        $stubbedRecord = [
            'transportManager' => ['id' => 987],
            'action' => 'A'
        ];
        $stubbedData = [
            $stubbedRecord
        ];

        // Mocks
        $mockTma = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);
        $mockTml = m::mock();
        $this->sm->setService('Entity\TransportManagerLicence', $mockTml);

        // Expecations
        $mockTma->shouldReceive('getGrantDataForApplication')
            ->with($id)
            ->andReturn($stubbedData);

        $mockTml->shouldReceive('getByTransportManagerAndLicence')
            ->with(987, $licenceId)
            ->andReturn(['foo' => 'bar']);

        $this->sut->grant($id, $licenceId);
    }

    public function testGrantWithCreateWithoutExistingTmOnLicenceWithoutOtherLicences()
    {
        // Params
        $id = 123;
        $licenceId = 321;

        // Data
        $stubbedRecord = [
            'id' => 1010,
            'action' => 'A',
            'version' => 1,
            'application' => 234,
            'tmApplicationStatus' => 'foo',
            'transportManager' => ['id' => 987],
            'operatingCentres' => [
                [
                    'id' => 2020
                ],
                [
                    'id' => 3030
                ]
            ],
            'otherLicences' => []
        ];
        $stubbedFlatRecord = [
            'id' => 1010,
            'action' => 'A',
            'version' => 1,
            'application' => 234,
            'tmApplicationStatus' => 'foo',
            'transportManager' => 987,
            'operatingCentres' => [
                [
                    'id' => 2020
                ],
                [
                    'id' => 3030
                ]
            ],
            'otherLicences' => []
        ];
        $stubbedData = [
            $stubbedRecord
        ];
        $expectedSaveData = [
            'licence' => $licenceId,
            'transportManager' => 987,
            'operatingCentres' => [2020, 3030]
        ];

        // Mocks
        $mockTma = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);
        $mockTml = m::mock();
        $this->sm->setService('Entity\TransportManagerLicence', $mockTml);
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        // Expecations
        $mockTma->shouldReceive('getGrantDataForApplication')
            ->with($id)
            ->andReturn($stubbedData);

        $mockTml->shouldReceive('getByTransportManagerAndLicence')
            ->with(987, $licenceId)
            ->andReturn([])
            ->shouldReceive('save')
            ->with($expectedSaveData)
            ->andReturn(654);

        $mockDataHelper->shouldReceive('replaceIds')
            ->with($stubbedRecord)
            ->andReturn($stubbedFlatRecord);

        $this->sut->grant($id, $licenceId);
    }

    public function testGrantWithCreateWithoutExistingTmOnLicenceWithOtherLicences()
    {
        // Params
        $id = 123;
        $licenceId = 321;

        // Data
        $stubbedOtherLicence = [
            'id' => 3030,
            'version' => 2,
            'transportManagerApplication' => ['foo' => 'bar'],
            'foo' => 'bar'
        ];
        $stubbedRecord = [
            'id' => 1010,
            'action' => 'A',
            'version' => 1,
            'application' => 234,
            'tmApplicationStatus' => 'foo',
            'transportManager' => ['id' => 987],
            'operatingCentres' => [
                [
                    'id' => 2020
                ],
                [
                    'id' => 3030
                ]
            ],
            'otherLicences' => [
                $stubbedOtherLicence
            ]
        ];
        $stubbedFlatRecord = [
            'id' => 1010,
            'action' => 'A',
            'version' => 1,
            'application' => 234,
            'tmApplicationStatus' => 'foo',
            'transportManager' => 987,
            'operatingCentres' => [
                [
                    'id' => 2020
                ],
                [
                    'id' => 3030
                ]
            ],
            'otherLicences' => [
                $stubbedOtherLicence
            ]
        ];
        $stubbedData = [
            $stubbedRecord
        ];
        $expectedSaveData = [
            'licence' => $licenceId,
            'transportManager' => 987,
            'operatingCentres' => [2020, 3030]
        ];
        $expectedOtherLicenceSaveData = [
            'transportManagerLicence' => 654,
            'foo' => 'bar'
        ];

        // Mocks
        $mockTma = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);
        $mockTml = m::mock();
        $this->sm->setService('Entity\TransportManagerLicence', $mockTml);
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);
        $mockOtherLicence = m::mock();
        $this->sm->setService('Entity\OtherLicence', $mockOtherLicence);

        // Expecations
        $mockTma->shouldReceive('getGrantDataForApplication')
            ->with($id)
            ->andReturn($stubbedData);

        $mockTml->shouldReceive('getByTransportManagerAndLicence')
            ->with(987, $licenceId)
            ->andReturn([])
            ->shouldReceive('save')
            ->with($expectedSaveData)
            ->andReturn(654);

        $mockDataHelper->shouldReceive('replaceIds')
            ->with($stubbedRecord)
            ->andReturn($stubbedFlatRecord)
            ->shouldReceive('replaceIds')
            ->with($stubbedOtherLicence)
            ->andReturn($stubbedOtherLicence);

        $mockOtherLicence->shouldReceive('save')
            ->with($expectedOtherLicenceSaveData);

        $this->sut->grant($id, $licenceId);
    }

    public function testGrantWithDelete()
    {
        // Params
        $id = 123;
        $licenceId = 321;

        // Data
        $stubbedRecord = [
            'transportManager' => [
                'id' => 654
            ],
            'action' => 'D'
        ];
        $stubbedData = [
            $stubbedRecord
        ];

        // Mocks
        $mockTma = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTma);
        $mockTml = m::mock();
        $this->sm->setService('Entity\TransportManagerLicence', $mockTml);

        // Expecations
        $mockTma->shouldReceive('getGrantDataForApplication')
            ->with($id)
            ->andReturn($stubbedData);

        $mockTml->shouldReceive('deleteList')
            ->with(['transportManager' => 654, 'licence' => $licenceId]);

        $this->sut->grant($id, $licenceId);
    }
}
