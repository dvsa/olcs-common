<?php

/**
 * Transport Manager Other Employment Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagerOtherEmploymentReviewService;
use CommonTest\Bootstrap;

/**
 * Transport Manager Other Employment Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerOtherEmploymentReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerOtherEmploymentReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfigFromData($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'transportManager' => [
                        'employments' => []
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-other-employment-none-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'transportManager' => [
                        'employments' => [
                            [
                                'employerName' => 'Tesco',
                                'contactDetails' => [
                                    'address' => [
                                        'addressLine1' => 'Foo street',
                                        'town' => 'Footown'
                                    ]
                                ],
                                'position' => 'Boss',
                                'hoursPerWeek' => 'All night long'
                            ],
                            [
                                'employerName' => 'Asda',
                                'contactDetails' => [
                                    'address' => [
                                        'addressLine1' => 'Foo street',
                                        'town' => 'Footown'
                                    ]
                                ],
                                'position' => 'Bossing around',
                                'hoursPerWeek' => '24/7'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'Tesco',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-other-employment-address',
                                                'value' => 'Foo street, Footown'
                                            ],
                                            [
                                                'label' => 'tm-review-other-employment-position',
                                                'value' => 'Boss'
                                            ],
                                            [
                                                'label' => 'tm-review-other-employment-hours-per-week',
                                                'value' => 'All night long'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'Asda',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-other-employment-address',
                                                'value' => 'Foo street, Footown'
                                            ],
                                            [
                                                'label' => 'tm-review-other-employment-position',
                                                'value' => 'Bossing around'
                                            ],
                                            [
                                                'label' => 'tm-review-other-employment-hours-per-week',
                                                'value' => '24/7'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
