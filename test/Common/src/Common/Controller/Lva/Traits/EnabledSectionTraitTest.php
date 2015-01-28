<?php

/**
 * Enabled Section Trait Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Enabled Section Trait Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EnabledSectionTraitTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\EnabledSectionTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider setEnabledAndCompleteProvider
     *
     * @param array $sections section config
     * @param array $completions application completions
     * @param array $expected
     */
    public function testSetEnabledAndCompleteFlagOnSections($sections, $completions, $expected)
    {
        $this->sm->setService(
            'Helper\Restriction',
            m::mock()
                ->shouldReceive('isRestrictionSatisfied')
                ->andReturn(true)
                ->getMock()
        );
        $this->sm->setService(
            'Helper\String',
            m::mock()
                ->shouldReceive('camelToUnderscore')
                ->with('typeOfLicence')->andReturn('type_of_licence')
                ->shouldReceive('camelToUnderscore')
                ->with('businessType')->andReturn('business_type')
                ->shouldReceive('camelToUnderscore')
                ->with('undertakings')->andReturn('undertakings')
                ->getMock()
        );

        $result = $this->sut->setEnabledAndCompleteFlagOnSections($sections, $completions);

        $this->assertEquals($expected, $result);
    }

    public function setEnabledAndCompleteProvider()
    {
        $sections = [
            'type_of_licence' => [],
            'business_type' => [
                'prerequisite' => 'type_of_licence'
            ],
        ];
        $completions = [
            'typeOfLicenceStatus' => 2, // ApplicationCompletionEntityService::STATUS_COMPLETE
            'businessTypeStatus' => 2,
        ];
        $expected = [
            'type_of_licence' => ['enabled' => true, 'complete' => true],
            'business_type'   => ['enabled' => true, 'complete' => true],
        ];

        return [
            'single prerequisite, 1 complete' => [
                [
                    'type_of_licence' => [],
                    'business_type' => [
                        'prerequisite' => 'type_of_licence'
                    ],
                ],
                [
                    'typeOfLicenceStatus' => 2, // ApplicationCompletionEntityService::STATUS_COMPLETE
                    'businessTypeStatus'  => 0, // ApplicationCompletionEntityService::STATUS_NOT_STARTED
                ],
                [
                    'type_of_licence' => ['enabled' => true, 'complete' => true],
                    'business_type'   => ['enabled' => true, 'complete' => false],
                ]
            ],
            'multiple prerequisites' => [
                [
                    'type_of_licence' => [],
                    'business_type' => [],
                    'business_details' => [
                        'prerequisite' => ['type_of_licence', 'business_type']
                    ],
                ],
                [
                    'typeOfLicenceStatus' => 2,
                    'businessTypeStatus'  => 2,
                    'businessDetails'     => 1, // ApplicationCompletionEntityService::STATUS_INCOMPLETE
                ],
                [
                    'type_of_licence'  => ['enabled' => true, 'complete' => true],
                    'business_type'    => ['enabled' => true, 'complete' => true],
                    'business_details' => ['enabled' => true, 'complete' => false],
                ]
            ],
            'inaccessible prerequisite' => [
                [
                    'type_of_licence' => [],
                    'business_type' => [],
                    'business_details' => [
                        'prerequisite' => 'foo'
                    ],
                ],
                [
                    'typeOfLicenceStatus' => 2,
                    'businessTypeStatus'  => 2,
                    'businessDetails'     => 1,
                ],
                [
                    'type_of_licence'  => ['enabled' => true, 'complete' => true],
                    'business_type'    => ['enabled' => true, 'complete' => true],
                    'business_details' => ['enabled' => true, 'complete' => false],
                ]
            ],
            'multiple inaccessible prerequisites' => [
                [
                    'type_of_licence' => [],
                    'business_type' => [],
                    'business_details' => [
                        'prerequisite' => [
                            ['foo', 'bar']
                        ],
                    ],
                ],
                [
                    'typeOfLicenceStatus' => 2,
                    'businessTypeStatus'  => 2,
                    'businessDetails'     => 1,
                ],
                [
                    'type_of_licence'  => ['enabled' => true, 'complete' => true],
                    'business_type'    => ['enabled' => true, 'complete' => true],
                    'business_details' => ['enabled' => true, 'complete' => false],
                ]
            ],
        ];
    }
}
