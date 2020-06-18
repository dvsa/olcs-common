<?php

/**
 * Licence Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\LicenceConditionsUndertakingsReviewService;

/**
 * Licence Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakingsReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceConditionsUndertakingsReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        // Params
        $data = [
            [
                'list' => [
                    'foo' => 'bar1'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar2'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar3'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar4'
                ]
            ]
        ];
        $inputData = ['foo' => 'bar']; // Doesn't matter what this is
        $expected = [
            'subSections' => [
                'BAR1',
                'BAR2',
                'BAR3',
                'BAR4'
            ]
        ];

        // Mocks
        $mockConditionsUndertakings = m::mock();
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $this->sm->setService('Review\ConditionsUndertakings', $mockConditionsUndertakings);

        // Expectations
        $mockConditionsUndertakings->shouldReceive('splitUpConditionsAndUndertakings')
            ->with($inputData, false)
            ->andReturn($data)
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo' => 'bar1'], 'application', 'conditions', 'added')
            ->andReturn('BAR1')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo' => 'bar2'], 'application', 'undertakings', 'added')
            ->andReturn('BAR2')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar3'], 'application', 'conditions', 'added')
            ->andReturn('BAR3')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar4'], 'application', 'undertakings', 'added')
            ->andReturn('BAR4');

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($inputData));
    }
}
