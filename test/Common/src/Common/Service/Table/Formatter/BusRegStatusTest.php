<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\BusRegStatus;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see BusRegStatus
 */
class BusRegStatusTest extends MockeryTestCase
{
    /**
     * Tests the formatting for the different possible input array formats
     *
     * @dataProvider formatProvider
     *
     * @param $data
     */
    public function testFormat($data)
    {
        $sut = new BusRegStatus();

        $regStatus = 'status id';
        $regStatusDesc = 'status description';
        $statusLabel = 'status label';

        $statusArray = [
            'id' => $regStatus,
            'description' => '_TRNSLT_' . $regStatusDesc,
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->expects('get->translate')
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSLT_' . $key;
                }
            );

        $sm->expects('get->get->__invoke')
            ->with($statusArray)
            ->andReturn($statusLabel);

        $this->assertEquals($statusLabel, $sut::format($data, [], $sm));
    }

    /**
     * Data provider for testFormat
     *
     * @return array
     */
    public function formatProvider()
    {
        $regStatus = 'status id';
        $regStatusDesc = 'status description';

        $busSearchViewFormat = [
            'busRegStatus' => $regStatus,
            'busRegStatusDesc' => $regStatusDesc
        ];

        $txcInboxFormat = [
            'status' => [
                'id' => $regStatus,
                'description' => $regStatusDesc
            ]
        ];

        $ebsrSubmissionFormat = [
            'busReg' => $txcInboxFormat
        ];

        return [
            [$busSearchViewFormat],
            [$txcInboxFormat],
            [$ebsrSubmissionFormat],
        ];
    }
}
