<?php

/**
 * Variation Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\VariationConditionsUndertakingsReviewService;

/**
 * Variation Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakingsReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationConditionsUndertakingsReviewService();
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
                ],
                'U' => [
                    'foo1' => 'bar1'
                ],
                'D' => [
                    'foo2' => 'bar1'
                ]
            ],
            [
                'A' => [
                    'foo' => 'bar2'
                ],
                'U' => [
                    'foo1' => 'bar2'
                ],
                'D' => [
                    'foo2' => 'bar2'
                ]
            ],
            [
                'A' => [
                    'foo' => 'bar3'
                ],
                'U' => [
                    'foo1' => 'bar3'
                ],
                'D' => [
                    'foo2' => 'bar3'
                ]
            ],
            [
                'A' => [
                    'foo' => 'bar4'
                ],
                'U' => [
                    'foo1' => 'bar4'
                ],
                'D' => [
                    'foo2' => 'bar4'
                ]
            ]
        ];
        $inputData = ['foo' => 'bar']; // Doesn't matter what this is
        $expected = [
            'subSections' => [
                'BAR1',
                '1BAR1',
                '2BAR1',
                'BAR2',
                '1BAR2',
                '2BAR2',
                'BAR3',
                '1BAR3',
                '2BAR3',
                'BAR4',
                '1BAR4',
                '2BAR4'
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
            ->with(['foo' => 'bar1'], 'variation', 'conditions', 'added')
            ->andReturn('BAR1')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo1' => 'bar1'], 'variation', 'conditions', 'updated')
            ->andReturn('1BAR1')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo2' => 'bar1'], 'variation', 'conditions', 'deleted')
            ->andReturn('2BAR1')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo' => 'bar2'], 'variation', 'undertakings', 'added')
            ->andReturn('BAR2')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo1' => 'bar2'], 'variation', 'undertakings', 'updated')
            ->andReturn('1BAR2')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo2' => 'bar2'], 'variation', 'undertakings', 'deleted')
            ->andReturn('2BAR2')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar3'], 'variation', 'conditions', 'added')
            ->andReturn('BAR3')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo1' => 'bar3'], 'variation', 'conditions', 'updated')
            ->andReturn('1BAR3')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo2' => 'bar3'], 'variation', 'conditions', 'deleted')
            ->andReturn('2BAR3')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar4'], 'variation', 'undertakings', 'added')
            ->andReturn('BAR4')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo1' => 'bar4'], 'variation', 'undertakings', 'updated')
            ->andReturn('1BAR4')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo2' => 'bar4'], 'variation', 'undertakings', 'deleted')
            ->andReturn('2BAR4');

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($inputData));
    }
}
