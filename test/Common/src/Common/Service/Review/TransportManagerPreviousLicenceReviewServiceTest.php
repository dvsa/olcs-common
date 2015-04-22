<?php

/**
 * Transport Manager Previous Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagerPreviousLicenceReviewService;
use CommonTest\Bootstrap;

/**
 * Transport Manager Previous Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerPreviousLicenceReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerPreviousLicenceReviewService();

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
                        'otherLicences' => [

                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-previous-licence-none-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'transportManager' => [
                        'otherLicences' => [
                            [
                                'licNo' => 'AB12345678',
                                'holderName' => 'Some holder'
                            ],
                            [
                                'licNo' => 'BA12345678',
                                'holderName' => 'Some other holder'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'AB12345678',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-licence-licNo',
                                                'value' => 'AB12345678'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-licence-holder',
                                                'value' => 'Some holder'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'BA12345678',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-licence-licNo',
                                                'value' => 'BA12345678'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-licence-holder',
                                                'value' => 'Some other holder'
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
