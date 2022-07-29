<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\EbsrDocumentStatus;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see EbsrDocumentStatus
 */
class EbsrDocumentStatusTest extends MockeryTestCase
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
        $sut = new EbsrDocumentStatus();

        $statusLabel = 'status label';
        $statusArray = [
            'colour' => $colour,
            'value' => $label
        ];

        $sm = m::mock(ServiceLocatorInterface::class);

        $sm->expects('get->get->__invoke')
            ->with($statusArray)
            ->andReturn($statusLabel);

        $data = [
            'ebsrSubmissionStatus' => [
                'id' => $ebsrStatus,
            ],
        ];

        $this->assertEquals($statusLabel, $sut->format($data, [], $sm));
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
