<?php

/**
 * Transport Manager Main Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagerMainReviewService;
use CommonTest\Bootstrap;
use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Main Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerMainReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerMainReviewService();

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
                        'documents' => [
                            [
                                'filename' => 'File1',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
                                ]
                            ],
                            [
                                'filename' => 'File2',
                                'category' => [
                                    'id' => 'FOOCAT'
                                ],
                                'subCategory' => [
                                    'id' => 'BARCAT'
                                ]
                            ],
                            [
                                'filename' => 'File3',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
                                ]
                            ]
                        ],
                        'workCd' => [
                            'address' => [
                                'addressLine1' => '123 work street'
                            ]
                        ],
                        'homeCd' => [
                            'address' => [
                                'addressLine1' => '123 home street'
                            ],
                            'person' => [
                                'title' => [
                                    'description' => 'Mr'
                                ],
                                'forename' => 'Foo',
                                'familyName' => 'Bar',
                                'birthDate' => '1989-08-23',
                                'birthPlace' => 'Footown'
                            ],
                            'emailAddress' => 'foo@bar.com'
                        ]
                    ]
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'tm-review-main-name',
                                'value' => 'Mr Foo Bar'
                            ],
                            [
                                'label' => 'tm-review-main-birthDate',
                                'value' => '23/08/1989'
                            ],
                            [
                                'label' => 'tm-review-main-birthPlace',
                                'value' => 'Footown'
                            ],
                            [
                                'label' => 'tm-review-main-email',
                                'value' => 'foo@bar.com'
                            ],
                            [
                                'label' => 'tm-review-main-certificate',
                                'noEscape' => true,
                                'value' => 'File1<br>File3'
                            ],
                            [
                                'label' => 'tm-review-main-home-address',
                                'value' => '123 home street'
                            ],
                            [
                                'label' => 'tm-review-main-work-address',
                                'value' => '123 work street'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'transportManager' => [
                        'documents' => [],
                        'workCd' => [
                            'address' => [
                                'addressLine1' => '123 work street'
                            ]
                        ],
                        'homeCd' => [
                            'address' => [
                                'addressLine1' => '123 home street'
                            ],
                            'person' => [
                                'title' => [
                                    'description' => 'Mr'
                                ],
                                'forename' => 'Foo',
                                'familyName' => 'Bar',
                                'birthDate' => '1989-08-23',
                                'birthPlace' => 'Footown'
                            ],
                            'emailAddress' => 'foo@bar.com'
                        ]
                    ]
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'tm-review-main-name',
                                'value' => 'Mr Foo Bar'
                            ],
                            [
                                'label' => 'tm-review-main-birthDate',
                                'value' => '23/08/1989'
                            ],
                            [
                                'label' => 'tm-review-main-birthPlace',
                                'value' => 'Footown'
                            ],
                            [
                                'label' => 'tm-review-main-email',
                                'value' => 'foo@bar.com'
                            ],
                            [
                                'label' => 'tm-review-main-certificate',
                                'noEscape' => true,
                                'value' => 'tm-review-main-no-files-translated'
                            ],
                            [
                                'label' => 'tm-review-main-home-address',
                                'value' => '123 home street'
                            ],
                            [
                                'label' => 'tm-review-main-work-address',
                                'value' => '123 work street'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
