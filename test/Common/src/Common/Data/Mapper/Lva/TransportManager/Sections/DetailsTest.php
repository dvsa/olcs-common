<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\Details;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class DetailsTest extends MockeryTestCase
{
    private $sut;
    private $mockTranslator;

    /**
     * setUp
     */
    public function setUp()
    {

        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new Details($this->mockTranslator);
    }

    public function testObjectPopulated()
    {
        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->twice()->andReturn('__TEST__');

        $actual = $this->sut->populate([
            'transportManager' =>
                [

                    'documents' => [],
                    'homeCd' => [
                        'emailAddress' => '__TEST__',
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ],

                        'person' => [
                            'forename' => '__TEST__',
                            'familyName' => '__TEST__',
                            'birthDate' => '2000-10-15',
                            'birthPlace' => '__TEST__',
                        ]
                    ],
                    'workCd' => [
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ]
                    ]
                ]
        ]);

        $this->assertInstanceOf(Details::class, $actual);
        $this->assertEquals([
            'lva-tmverify-details-checkanswer-name' => '__TEST__ __TEST__',
            'lva-tmverify-details-checkanswer-birthDate' => '15 Oct 2000',
            'lva-tmverify-details-checkanswer-birthPlace' => '__TEST__',
            'lva-tmverify-details-checkanswer-emailAddress' => '__TEST__',
            'lva-tmverify-details-checkanswer-certificate' => 'No certificates attached',
            'lva-tmverify-details-checkanswer-homeCd' => '__TEST__',
            'lva-tmverify-details-checkanswer-workCd' => '__TEST__',
        ], $actual->sectionSerialize());
    }

    public function testFormatAddress()
    {
        $data = [
            'transportManager' =>
                [
                    'documents' => [],
                    'homeCd' => [
                        'emailAddress' => '__TEST__',
                        'address' => [
                            'addressLine1' => 'addressLine1',
                            'addressLine2' => 'addressLine2',
                            'addressLine3' => '',
                            'addressLine4' => 'addressLine4',
                            'Town' => 'test',
                            'postcode' => 'test',
                            '' => '',
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ],

                        'person' => [
                            'forename' => '__TEST__',
                            'familyName' => '__TEST__',
                            'birthDate' => '2000-10-15',
                        ]
                    ],
                    'workCd' => [
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ]
                    ]
                ]
        ];

        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-address', [
            'addressLine1' => 'addressLine1',
            'addressLine2' => 'addressLine2',
            'addressLine4' => 'addressLine4',
            'postcode' => 'test',
            'country' => '__TEST__',
            'addressLine3' => '',
        ])->once()->andReturn('__TEST__');
        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-address', [
            'country' => '__TEST__',
        ])->once()->andReturn('__TEST__');

        $this->assertEquals([
            'lva-tmverify-details-checkanswer-name' => '__TEST__ __TEST__',
            'lva-tmverify-details-checkanswer-birthDate' => '15 Oct 2000',
            'lva-tmverify-details-checkanswer-birthPlace' => null,
            'lva-tmverify-details-checkanswer-emailAddress' => '__TEST__',
            'lva-tmverify-details-checkanswer-certificate' => 'No certificates attached',
            'lva-tmverify-details-checkanswer-homeCd' => '__TEST__',
            'lva-tmverify-details-checkanswer-workCd' => '__TEST__',
        ], $this->sut->populate($data)->sectionSerialize());
    }

    public function testCertificateNotAdded()
    {
        $data = [
            'transportManager' =>
                [
                    'documents' => [],
                    'homeCd' => [
                        'emailAddress' => '__TEST__',
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ],

                        'person' => [
                            'forename' => '__TEST__',
                            'familyName' => '__TEST__',
                        ]
                    ],
                    'workCd' => [
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ]
                    ]
                ]
        ];

        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-address', [
            'country' => '__TEST__',
        ])->times(2)->andReturn('__TEST__');
        $actual = $this->sut->populate($data);

        $this->assertContains('No certificates attached', $actual->sectionSerialize());
    }

    public function testCertificateAdded()
    {
        $data = [
            'transportManager' =>
                [
                    'documents' => [


                        [
                            'application' => ['id' => 1],
                            'category' => ['id' => 5],
                            'subCategory' => ['id' => 98]
                        ]

                    ],
                    'homeCd' => [
                        'emailAddress' => '__TEST__',
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ],

                        'person' => [
                            'forename' => '__TEST__',
                            'familyName' => '__TEST__',
                        ]
                    ],
                    'workCd' => [
                        'address' => [
                            'countryCode' => [
                                'countryDesc' => '__TEST__'
                            ],
                        ]
                    ]
                ]
        ];

        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-address', [
            'country' => '__TEST__',
        ])->times(2)->andReturn('__TEST__');
        $actual = $this->sut->populate($data);

        $this->assertEquals(
            'Certificate Added',
            $actual->sectionSerialize()['lva-tmverify-details-checkanswer-certificate']
        );
    }

    public function tearDown()
    {
        $this->mockTranslator = null;
    }
}
