<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrDocumentLink;
use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class EbsrDocumentLinkTest
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

        $submissionId = 123;
        $documentDescription = 'description';
        $url = 'http://url.com';
        $statusLabel = 'status label';
        $statusArray = [
            'colour' => $colour,
            'value' => $label
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->once()
            ->with(EbsrDocumentLink::URL_ROUTE, ['id' => $submissionId, 'action' => EbsrDocumentLink::URL_ACTION])
            ->andReturn($url);

        $sm->shouldReceive('get->get->__invoke')
            ->once()
            ->with($statusArray)
            ->andReturn($statusLabel);

        $data = [
            'document' => [
                'description' => $documentDescription
            ],
            'ebsrSubmissionStatus' => [
                'id' => $ebsrStatus,
            ],
            'id' => $submissionId
        ];

        $expected = sprintf(EbsrDocumentLink::LINK_PATTERN, $url, $documentDescription, $statusLabel);

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
