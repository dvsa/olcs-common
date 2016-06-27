<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrDocumentLink;
use Common\RefData;

/**
 * Class DashboardTmActionLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class EbsrDocumentLinkTest extends MockeryTestCase
{
    /**
     * Tests format
     *
     * @dataProvider dataProviderFormat
     *
     * @param string $ebsrStatus
     * @param string $colour
     * @param string $label
     *
     * @return void
     */
    public function testFormat($ebsrStatus, $colour, $label)
    {
        $sut = new EbsrDocumentLink();

        $documentId = 123;
        $documentDescription = 'description';
        $url = 'http://url.com';

        $sm = m::mock('StdClass');
        $sm->shouldReceive('get->fromRoute')
            ->once()
            ->with('getfile', ['identifier' => $documentId])
            ->andReturn($url);
        
        $data = [
            'document' => [
                'id' => $documentId,
                'description' => $documentDescription
            ],
            'ebsrSubmissionStatus' => [
                'id' => $ebsrStatus,
            ]
        ];

        $expected = sprintf(EbsrDocumentLink::LINK_PATTERN, $url, $documentDescription, $colour, $label);

        $this->assertEquals($expected, $sut->format($data, [], $sm));
    }

    /**
     * Data provider for testFormat
     *
     * @return array
     */
    public function dataProviderFormat()
    {
        return [
            [RefData::EBSR_STATUS_PROCESSING, 'orange', 'processing'],
            [RefData::EBSR_STATUS_VALIDATING, 'orange', 'processing'],
            [RefData::EBSR_STATUS_SUBMITTED, 'orange', 'processing'],
            [RefData::EBSR_STATUS_PROCESSED, 'green', 'successful'],
            [RefData::EBSR_STATUS_FAILED, 'red', 'failed'],
        ];
    }
}
