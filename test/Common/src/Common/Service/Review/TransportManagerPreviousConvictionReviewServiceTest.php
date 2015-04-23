<?php

/**
 * Transport Manager Previous Conviction Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagerPreviousConvictionReviewService;
use CommonTest\Bootstrap;

/**
 * Transport Manager Previous Conviction Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerPreviousConvictionReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerPreviousConvictionReviewService();

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
                        'previousConvictions' => [

                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-previous-conviction-none-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'transportManager' => [
                        'previousConvictions' => [
                            [
                                'categoryText' => 'Some conviction',
                                'convictionDate' => '2014-10-01',
                                'notes' => 'Conviction notes',
                                'courtFpn' => 'Some court name',
                                'penalty' => 'Some penalty'
                            ],
                            [
                                'categoryText' => 'Some other conviction',
                                'convictionDate' => '2014-10-02',
                                'notes' => 'More conviction notes',
                                'courtFpn' => 'Some other court name',
                                'penalty' => 'Some other penalty'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'Some conviction',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-conviction-date',
                                                'value' => '01/10/2014'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence',
                                                'value' => 'Some conviction'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence-details',
                                                'value' => 'Conviction notes'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-court',
                                                'value' => 'Some court name'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-penalty',
                                                'value' => 'Some penalty'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'Some other conviction',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-conviction-date',
                                                'value' => '02/10/2014'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence',
                                                'value' => 'Some other conviction'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence-details',
                                                'value' => 'More conviction notes'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-court',
                                                'value' => 'Some other court name'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-penalty',
                                                'value' => 'Some other penalty'
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
