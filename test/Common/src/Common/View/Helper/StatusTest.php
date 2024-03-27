<?php

/**
 * Test Status view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use Common\RefData;
use Common\View\Helper\Status;

/**
 * Test Status view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class StatusTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public $sut;
    private $mockView;

    /**
     * Setup the view helper
     */
    protected function setUp(): void
    {
        $this->sut = new Status();

        $this->mockView = \Mockery::mock(\Laminas\View\Renderer\PhpRenderer::class);
        $this->sut->setView($this->mockView);
    }

    /**
     * @dataProvider dataProviderStatus
     */
    public function testStatus($status, $color): void
    {
        $html = $this->sut->__invoke($status);

        $expected = empty($color) ? '' : '<strong class="govuk-tag govuk-tag--' . $color . '">value</strong>';
        $this->assertEquals($expected, $html);
    }

    public function dataProviderStatus()
    {
        return [
            [
                [],
                ''
            ],
            [
                ['id' => RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT, 'description' => ''],
                ''
            ],
            [
                ['colour' => 'red', 'value' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_UNDER_CONSIDERATION, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_NOT_SUBMITTED, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_SUSPENDED, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_VALID, 'description' => 'value'],
                'green'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_CURTAILED, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_GRANTED, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_SURRENDERED, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_WITHDRAWN, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_REFUSED, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_REVOKED, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_NOT_TAKEN_UP, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_TERMINATED, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT, 'description' => 'value'],
                'red'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_ADMIN, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_REGISTERED, 'description' => 'value'],
                'green'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_REFUSED, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_CANCELLATION, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_WITHDRAWN, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_VARIATION, 'description' => 'value'],
                'orange'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_CNS, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_CANCELLED, 'description' => 'value'],
                'grey'
            ],
            [
                ['id' => RefData::BUSREG_STATUS_NEW, 'description' => 'value'],
                'orange'
            ],
        ];
    }
}
