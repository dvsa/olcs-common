<?php

/**
 * Application Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationConditionsUndertakingsReviewService;

/**
 * Application Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakingsReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationConditionsUndertakingsReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithNoneAdded()
    {
        // Params
        $data = [
            [],
            [],
            [],
            []
        ];
        $inputData = ['foo' => 'bar']; // Doesn't matter what this is
        $expected = [
            'freetext' => 'review-none-added-translated'
        ];

        // Mocks
        $mockConditionsUndertakings = m::mock();
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $this->sm->setService('Review\ConditionsUndertakings', $mockConditionsUndertakings);

        // Expectations
        $mockConditionsUndertakings->shouldReceive('splitUpConditionsAndUndertakings')
            ->with($inputData)
            ->andReturn($data);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($inputData));
    }

    public function testGetConfigFromData()
    {
        // Params
        $data = [
            [
                'A' => [
                    'foo' => 'bar1'
                ]
            ],
            [
                'A' => [
                    'foo' => 'bar2'
                ]
            ],
            [
                'A' => [
                    'foo' => 'bar3'
                ]
            ],
            [
                'A' => [
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
            ->with($inputData)
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
