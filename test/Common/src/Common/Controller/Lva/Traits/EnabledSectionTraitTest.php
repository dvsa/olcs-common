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

    public function testSetEnabledAndCompleteFlagOnSections()
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

        $sections = [
            'type_of_licence' => [],
            'business_type' => [
                'prerequisite' => ['type_of_licence']
            ],
            'undertakings' => [],
        ];
        $completions = [
            'typeOfLicenceStatus' => 2, // ApplicationCompletionEntityService::STATUS_COMPLETE
            'businessTypeStatus' => 2,
            'undertakingsStatus' => 0,
        ];

        $result = $this->sut->setEnabledAndCompleteFlagOnSections($sections, $completions);

        $expected = [
            'type_of_licence' => ['enabled' => true, 'complete' => true],
            'business_type'   => ['enabled' => true, 'complete' => true],
            'undertakings'    => ['enabled' => true, 'complete' => false],
        ];

        $this->assertEquals($expected, $result);
    }
}
