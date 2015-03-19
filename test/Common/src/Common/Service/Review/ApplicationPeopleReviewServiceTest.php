<?php

/**
 * Application People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationPeopleReviewService;

/**
 * Application People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeopleReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationPeopleReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        // Shows because we are Adding
        $person1 = [
            'action' => 'A',
            'originalPerson' => null,
            'person' => ['forename' => 'Added']
        ];
        // Shows because we have Updated
        $person2 = [
            'action' => 'U',
            'originalPerson' => [
                'id' => 123
            ],
            'person' => ['forename' => 'Updated']
        ];
        // Is ignored because it is deleted
        $person3 = [
            'action' => 'D',
            'originalPerson' => null,
            'person' => ['id' => 321]
        ];
        // Shows as it is an unchanged, existing record
        $person4 = [
            'person' => [
                'id' => 987,
                'forename' => 'Bob'
            ]
        ];
        // Is ignored as there is an updated version
        $person5 = [
            'person' => [
                'id' => 123,
                'forename' => 'Bob'
            ]
        ];
        // Is ignored as there is a delete delta
        $person6 = [
            'person' => [
                'id' => 321,
                'forename' => 'Bob'
            ]
        ];

        $data = [
            'applicationOrganisationPersons' => [
                $person1, $person2, $person3
            ],
            'licence' => [
                'organisation' => [
                    'organisationPersons' => [
                        $person4, $person5, $person6
                    ]
                ]
            ]
        ];
        $expected = [
            'subSections' => [
                [
                    'mainItems' => [
                        'PERSON1',
                        'PERSON2',
                        'PERSON4'
                    ]
                ]
            ]
        ];

        $mockPeopleReview = m::mock();
        $this->sm->setService('Review\People', $mockPeopleReview);

        $mockPeopleReview->shouldReceive('shouldShowPosition')
            ->with($data)
            ->andReturn(true)
            ->shouldReceive('getConfigFromData')
            ->with($person1, true)
            ->andReturn('PERSON1')
            ->shouldReceive('getConfigFromData')
            ->with($person2, true)
            ->andReturn('PERSON2')
            ->shouldReceive('getConfigFromData')
            ->with($person4, true)
            ->andReturn('PERSON4');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
