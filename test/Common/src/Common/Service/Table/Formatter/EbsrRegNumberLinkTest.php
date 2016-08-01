<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrRegNumberLink;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class EbsrRegNumberLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class EbsrRegNumberLinkTest extends MockeryTestCase
{
    /**
     * Tests empty string returned if there's no variation number set
     */
    public function testFormatWithNoId()
    {
        $sut = new EbsrRegNumberLink();
        $this->assertEquals('', $sut->format([]));
    }

    /**
     * Tests the formatting for the different possible input array formats
     *
     * @dataProvider formatProvider
     *
     * @param $data
     */
    public function testFormat($data)
    {
        $sut = new EbsrRegNumberLink();

        $regStatus = 'status id';
        $regStatusDesc = 'status description';
        $id = 1234;
        $regNo = 5678;
        $statusLabel = 'status label';
        $url = 'the url';

        $statusArray = [
            'id' => $regStatus,
            'description' => $regStatusDesc
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->once()
            ->with(EbsrRegNumberLink::URL_ROUTE, ['busRegId' => $id])
            ->andReturn($url);

        $sm->shouldReceive('get->get->__invoke')
            ->once()
            ->with($statusArray)
            ->andReturn($statusLabel);

        $expected = sprintf(EbsrRegNumberLink::LINK_PATTERN, $url, $regNo) . $statusLabel;

        $this->assertEquals($expected, $sut->format($data, [], $sm));
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
        $id = 1234;
        $regNo = 5678;

        $busSearchViewFormat = [
            'id' => $id,
            'regNo' => $regNo,
            'busRegStatus' => $regStatus,
            'busRegStatusDesc' => $regStatusDesc
        ];

        $txcInboxFormat = [
            'id' => $id,
            'regNo' => $regNo,
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
