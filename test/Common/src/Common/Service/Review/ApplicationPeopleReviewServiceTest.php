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

    /**
     * @dataProvider provider
     */
    public function testGetConfigFromData($data, $noOfPeople, $expected)
    {
        $mockPeopleReview = m::mock();
        $this->sm->setService('Review\People', $mockPeopleReview);

        $mockPeopleReview->shouldReceive('shouldShowPosition')
            ->with($data)
            ->andReturn(true)
            ->shouldReceive('getConfigFromData')
            ->times($noOfPeople)
            ->with('PERSON', true)
            ->andReturn('PERSON_CONFIG');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'applicationOrganisationPersons' => [
                        'PERSON'
                    ],
                    'licence' => [
                        'organisation' => [
                            'organisationPersons' => [
                                'PERSON',
                                'PERSON'
                            ]
                        ]
                    ]
                ],
                3,
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                'PERSON_CONFIG',
                                'PERSON_CONFIG',
                                'PERSON_CONFIG'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
