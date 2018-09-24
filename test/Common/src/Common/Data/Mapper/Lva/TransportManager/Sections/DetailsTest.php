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
        )->twice()->andReturn('');
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
        foreach (get_object_vars($this->sut) as $property) {
            $this->assertNotEmpty($property);
        }
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

        $this->assertEquals('__TEST__', $this->sut->populate($data)->getHomeCd());
    }
}
