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
        $data = ['transportManager' =>
                [
                    'documents' => [],
                    'homeCd' => [
                        'emailAddress' => '__TEST__',
                        'address' => [
                            'addressLine1' =>'test',
                            'addressLine2' =>'test',
                            'addressLine3' =>'test',
                            'addressLine4' =>'test',
                            'Town' =>'test',
                            'postcode'
                            ''=>'',
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
        )->with([])->twice()->andReturn('');
        $this->sut->populate($data)->getHomeCd();
    }
}
