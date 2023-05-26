<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\EbsrDocumentStatus;
use Common\View\Helper\Status;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see EbsrDocumentStatus
 */
class EbsrDocumentStatusTest extends MockeryTestCase
{
    protected $viewHelperManager;
    protected $sut;

    protected function setUp(): void
    {
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->sut = new EbsrDocumentStatus($this->viewHelperManager);
    }

    protected function tearDown(): void
    {
        m::close();
    }
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
        $statusLabel = 'status label';
        $statusArray = [
            'colour' => $colour,
            'value' => $label
        ];

        $statusHelper = m::mock(Status::class);

        $statusHelper->expects('__invoke')
            ->with($statusArray)
            ->andReturn($statusLabel);

        $this->viewHelperManager->shouldReceive('get')
            ->with('status')
            ->andReturn($statusHelper);

        $data = [
            'ebsrSubmissionStatus' => [
                'id' => $ebsrStatus,
            ],
        ];

        $this->assertEquals($statusLabel, $this->sut->format($data, []));
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
