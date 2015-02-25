<?php

/**
 * Grant People Process Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\GrantPeopleProcessingService;
use Common\Service\Helper\DataHelperService;

/**
 * Grant People Process Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GrantPeopleProcessingServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new GrantPeopleProcessingService();
        $this->sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGrantWithoutData()
    {
        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getAllByApplication')
            ->with(1)
            ->andReturn(['Count' => 0])
            ->getMock()
        );

        $this->sut->grant(1);
    }

    public function testGrantWithCreate()
    {
        $results = [
            'Count' => 1,
            'Results' => [
                [
                    'person' => [
                        'version' => 1,
                        'id' => 2,
                        'createdOn' => '2014-01-01',
                        'lastModifiedOn' => '2014-01-01',
                        'forename' => 'Test',
                        'familyName' => 'Person'
                    ],
                    'originalPerson' => null,
                    'organisation' => [
                        'id' => 3
                    ],
                    'action' => 'A',
                    'position' => 'a position'
                ]
            ]
        ];

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getAllByApplication')
            ->with(1)
            ->andReturn($results)
            ->getMock()
        );

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'forename' => 'Test',
                    'familyName' => 'Person'
                ]
            )
            ->andReturn(['id' => 100])
            ->getMock()
        );

        $this->setService(
            'Entity\OrganisationPerson',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'person' => 100,
                    'addedDate' => '2015-01-01',
                    'position' => 'a position',
                    'organisation' => 3
                ]
            )
            ->getMock()
        );

        $this->setService('Helper\Data', new DataHelperService());

        $this->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->sut->grant(1);
    }

    public function testGrantWithUpdate()
    {
        $results = [
            'Count' => 1,
            'Results' => [
                [
                    'person' => [
                        'version' => 1,
                        'id' => 2,
                        'createdOn' => '2014-01-01',
                        'lastModifiedOn' => '2014-01-01',
                        'forename' => 'Test',
                        'familyName' => 'Person'
                    ],
                    'originalPerson' => [
                        'id' => 1
                    ],
                    'organisation' => [
                        'id' => 3
                    ],
                    'action' => 'U',
                    'position' => 'a position'
                ]
            ]
        ];

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getAllByApplication')
            ->with(1)
            ->andReturn($results)
            ->getMock()
        );

        $this->setService(
            'Entity\Person',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'forename' => 'Test',
                    'familyName' => 'Person'
                ]
            )
            ->andReturn(['id' => 100])
            ->getMock()
        );

        $this->setService(
            'Entity\OrganisationPerson',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'person' => 100,
                    'addedDate' => '2015-01-01',
                    'position' => 'a position',
                    'organisation' => 3
                ]
            )
            ->shouldReceive('deleteByOrgAndPersonId')
            ->with(3, 1)
            ->getMock()
        );

        $this->setService('Helper\Data', new DataHelperService());

        $this->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->sut->grant(1);
    }

    public function testGrantWithDelete()
    {
        $results = [
            'Count' => 1,
            'Results' => [
                [
                    'person' => [
                        'version' => 1,
                        'id' => 2,
                        'createdOn' => '2014-01-01',
                        'lastModifiedOn' => '2014-01-01',
                        'forename' => 'Test',
                        'familyName' => 'Person'
                    ],
                    'originalPerson' => null,
                    'organisation' => [
                        'id' => 3
                    ],
                    'action' => 'D'
                ]
            ]
        ];

        $this->setService(
            'Entity\ApplicationOrganisationPerson',
            m::mock()
            ->shouldReceive('getAllByApplication')
            ->with(1)
            ->andReturn($results)
            ->getMock()
        );

        $this->setService(
            'Entity\OrganisationPerson',
            m::mock()
            ->shouldReceive('deleteByOrgAndPersonId')
            ->with(3, 2)
            ->getMock()
        );

        $this->sut->grant(1);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
