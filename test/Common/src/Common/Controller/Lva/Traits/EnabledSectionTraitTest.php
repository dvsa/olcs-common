<?php

/**
 * Enabled Section Trait Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\Common\Controller\Lva\Traits;

use Common\RefData;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\RestrictionHelperService;
use Common\Service\Helper\StringHelperService;
use CommonTest\Bootstrap;
use CommonTest\Common\Controller\Lva\Traits\Stubs\EnabledSectionTraitStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Enabled Section Trait Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EnabledSectionTraitTest extends MockeryTestCase
{
    public $mockRestrictionHelper;
    public $mockStringHelper;
    protected $sut;

    protected $sm;

    protected function setUp(): void
    {
        $this->mockRestrictionHelper = m::mock(RestrictionHelperService::class);
        $this->mockStringHelper = m::mock(StringHelperService::class);
        $this->sut = m::mock(EnabledSectionTraitStub::class, [$this->mockRestrictionHelper, $this->mockStringHelper])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @dataProvider setEnabledAndCompleteProvider
     *
     * @param array $sections section config
     * @param array $completions application completions
     * @param array $expected
     */
    public function testSetEnabledAndCompleteFlagOnSections($sections, $completions, $expected): void
    {
        $this->mockRestrictionHelper
                ->shouldReceive('isRestrictionSatisfied')
                ->andReturn(true);

        $this->mockStringHelper
                ->shouldReceive('camelToUnderscore')
                ->with('typeOfLicence')->andReturn('type_of_licence')
                ->shouldReceive('camelToUnderscore')
                ->with('businessType')->andReturn('business_type')
                ->shouldReceive('camelToUnderscore')
                ->with('undertakings')->andReturn('undertakings');

        $result = $this->sut->setEnabledAndCompleteFlagOnSections($sections, $completions);

        $this->assertEquals($expected, $result);
    }

    public function setEnabledAndCompleteProvider()
    {
        return [
            'single prerequisite, 1 complete' => [
                [
                    'type_of_licence' => [],
                    'business_type' => [
                        'prerequisite' => 'type_of_licence'
                    ],
                ],
                [
                    'typeOfLicenceStatus' => RefData::APPLICATION_COMPLETION_STATUS_COMPLETE,
                    'businessTypeStatus'  => RefData::APPLICATION_COMPLETION_STATUS_NOT_STARTED,
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
                    'businessDetails'     => RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE,
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
