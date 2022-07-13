<?php

/**
 * InspectionRequestId Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\InspectionRequestId;

/**
 * InspectionRequestId Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestIdTest extends MockeryTestCase
{
    /**
     * Test formatter
     *
     * @group inspectionRequestIdFormatter
     * @dataProvider formatProvider
     *
     * @param array $data
     * @param string $expectedRouteName
     * @param string $expectedUrlParams
     * @param string $expectedUrl
     * @param string $expectedOutput
     */
    public function testFormat(
        $data,
        $expectedRouteName,
        $expectedUrlParams,
        $expectedUrl,
        $expectedOutput
    ) {

        // mocks
        $sm = m::mock();
        $mockUrlHelper = m::mock();

        // expectations
        $mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with($expectedRouteName, $expectedUrlParams)
            ->andReturn($expectedUrl);

        $mockRequest = m::mock();

        $mockRouter = m::mock()
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn(
                m::mock()
                ->shouldReceive('getMatchedRouteName')
                ->once()
                ->andReturn($expectedRouteName)
                ->shouldReceive('getParams')
                ->andReturn(['application' => 3])
                ->getMock()
            )
            ->once()
            ->getMock();

        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper)
            ->once()
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter)
            ->once()
            ->shouldReceive('get')
            ->with('request')
            ->andReturn($mockRequest)
            ->once()
            ->getMock();

        $this->assertEquals($expectedOutput, InspectionRequestId::format($data, [], $sm));
    }

    public function formatProvider()
    {
        return [
            'licence inspection request' => [
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => null,
                ],
                'licence/processing/inspection-request',
                [
                    'action' => 'edit',
                    'licence' => 2,
                    'id' => 1,
                ],
                'url1',
                '<a href="url1" class="govuk-link js-modal-ajax">1</a>'
            ],
            'application inspection request' => [
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => ['id' => 3, 'isVariation' => false]
                ],
                'lva-application/processing/inspection-request',
                [
                    'action' => 'edit',
                    'application' => 3,
                    'id' => 1,
                ],
                'url2',
                '<a href="url2" class="govuk-link js-modal-ajax">1</a>'
            ]
        ];
    }
}
