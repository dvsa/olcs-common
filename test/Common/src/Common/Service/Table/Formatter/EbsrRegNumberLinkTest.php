<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrRegNumberLink;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @covers \Common\Service\Table\Formatter\EbsrRegNumberLink
 */
class EbsrRegNumberLinkTest extends MockeryTestCase
{
    /**
     * Tests empty string returned if there's no variation number set
     */
    public function testFormatWithNoId()
    {
        $sut = new EbsrRegNumberLink();
        $this->assertEquals('', $sut::format([]));
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

        $id = 1234;
        $regNo = 5678;
        $url = 'the url';

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->expects('get->fromRoute')
            ->with(EbsrRegNumberLink::URL_ROUTE, ['busRegId' => $id])
            ->andReturn($url);

        $expected = sprintf(EbsrRegNumberLink::LINK_PATTERN, $url, $regNo);

        $this->assertEquals($expected, $sut::format($data, [], $sm));
    }

    /**
     * Data provider for testFormat
     *
     * @return array
     */
    public function formatProvider()
    {
        $id = 1234;
        $regNo = 5678;

        $txcInboxFormat = [
            'id' => $id,
            'regNo' => $regNo,
        ];

        $ebsrSubmissionFormat = [
            'busReg' => $txcInboxFormat
        ];

        return [
            [$txcInboxFormat],
            [$ebsrSubmissionFormat],
        ];
    }
}
