<?php

/**
 * Variation People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Printing;

use CommonTest\Bootstrap;
use Common\Service\Lva\VariationPeopleLvaService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Variation People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleLvaServiceTest extends MockeryTestCase
{
    private $sm;
    private $sut;

    public function setup()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $this->sut = new VariationPeopleLvaService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetTableData()
    {
        $orgResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 1
                    ]
                ], [
                    'person' => [
                        'id' => 4
                    ]
                ]
            ]
        ];

        $appResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 2
                    ],
                    'originalPerson' => [
                        'id' => 1
                    ],
                    'action' => 'U'
                ], [
                    'person' => [
                        'id' => 3
                    ],
                    'action' => 'A'
                ]
            ]
        ];

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->with(123)
            ->andReturn($orgResults)
            ->shouldReceive('getAllForApplication')
            ->with(456)
            ->andReturn($appResults)
            ->getMock()
        );

        $expected = [
            [
                'person' => [
                    'id' => 1,
                    'source' => 'O'
                ],
                'action' => 'C'
            ], [
                'person' => [
                    'id' => 4,
                    'source' => 'O'
                ],
                'action' => 'E'
            ], [
                'person' => [
                    'id' => 2,
                    'source' => 'A'
                ],
                'originalPerson' => [
                    'id' => 1
                ],
                'action' => 'U'
            ], [
                'person' => [
                    'id' => 3,
                    'source' => 'A'
                ],
                'action' => 'A'
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->sut->getTableData(123, 456)
        );
    }

    public function testDeletePersonAttachedToApplication()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(100, 10)
            ->andReturn(['id' => 5])
            ->shouldReceive('deletePerson')
            ->with(5, 10)
            ->getMock()
        );

        $this->sut->deletePerson(123, 10, 100);
    }

    public function testDeletePersonAttachedToOrganisation()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(100, 10)
            ->andReturn(false)
            ->shouldReceive('variationDelete')
            ->with(10, 123, 100)
            ->getMock()
        );

        $this->sut->deletePerson(123, 10, 100);
    }

    public function testRestorePersonWithInvalidAction()
    {
        $orgResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 4
                    ]
                ]
            ]
        ];

        $appResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 5
                    ],
                    'action' => 'A'
                ]
            ]
        ];

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->with(123)
            ->andReturn($orgResults)
            ->shouldReceive('getAllForApplication')
            ->with(456)
            ->andReturn($appResults)
            ->getMock()
        );

        try {
            $this->sut->restorePerson(123, 5, 456);
        } catch (\Exception $e) {
            $this->assertEquals('Can\'t restore this record', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testRestorePersonWithDeletedAction()
    {
        $orgResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 5
                    ]
                ]
            ]
        ];

        $appResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 5
                    ],
                    'action' => 'D'
                ]
            ]
        ];

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->with(123)
            ->andReturn($orgResults)
            ->shouldReceive('getAllForApplication')
            ->with(456)
            ->andReturn($appResults)
            ->getMock()
        );

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('deleteByApplicationAndPersonId')
            ->with(456, 5)
            ->andReturn('foo')
            ->getMock()
        );

        $this->assertEquals(
            'foo',
            $this->sut->restorePerson(123, 5, 456)
        );
    }

    public function testRestorePersonWithCurrentAction()
    {
        $orgResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 5
                    ]
                ]
            ]
        ];

        $appResults = [
            'Results' => [
                [
                    'person' => [
                        'id' => 8
                    ],
                    'originalPerson' => [
                        'id' => 5
                    ],
                    'action' => 'U'
                ]
            ]
        ];

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->with(123)
            ->andReturn($orgResults)
            ->shouldReceive('getAllForApplication')
            ->with(456)
            ->andReturn($appResults)
            ->getMock()
        );

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('deleteByApplicationAndOriginalPersonId')
            ->with(456, 5)
            ->andReturn('foo')
            ->getMock()
        );

        $this->assertEquals(
            'foo',
            $this->sut->restorePerson(123, 5, 456)
        );
    }

    public function testSaveWithNoId()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('variationCreate')
            ->with(123, 456, [])
            ->getMock()
        );

        $this->sut->savePerson(123, [], 456);
    }

    public function testSaveWithIdRelatingToApplication()
    {
        $data = [
            'id' => 200,
            'forename' => 'A',
            'familyName' => 'Surname'
        ];

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(456, 200)
            ->andReturn(['id' => 100])
            ->shouldReceive('updatePerson')
            ->with(['id' => 100], $data)
            ->getMock()
        );

        $this->sut->savePerson(123, $data, 456);
    }

    public function testSaveWithIdRelatingToOrganisation()
    {
        $data = [
            'id' => 200,
            'forename' => 'A',
            'familyName' => 'Surname'
        ];

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(456, 200)
            ->andReturn(false)
            ->shouldReceive('variationUpdate')
            ->with(123, 456, $data)
            ->getMock()
        );

        $this->sut->savePerson(123, $data, 456);
    }

    public function testGetPersonPositionWithApplicationPerson()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(456, 100)
            ->andReturn(
                [
                    'position' => 'a position'
                ]
            )
            ->getMock()
        );

        $this->assertEquals('a position', $this->sut->getPersonPosition(123, 456, 100));
    }

    public function testGetPersonPositionWithOrganisationPerson()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getByApplicationAndPersonId')
            ->with(456, 100)
            ->andReturn(false)
            ->getMock()
        );

        $this->setService(
            'Entity\OrganisationPerson',
            m::mock()
            ->shouldReceive('getByOrgAndPersonId')
            ->with(123, 100)
            ->andReturn(
                [
                    'position' => 'org position'
                ]
            )
            ->getMock()
        );

        $this->assertEquals('org position', $this->sut->getPersonPosition(123, 456, 100));
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
