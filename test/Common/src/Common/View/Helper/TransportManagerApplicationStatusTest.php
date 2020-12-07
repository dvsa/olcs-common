<?php

namespace CommonTest\View\Helper;

use Common\RefData;
use Common\View\Helper\TransportManagerApplicationStatus;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\View\Renderer\RendererInterface;

/**
 * @covers \Common\View\Helper\TransportManagerApplicationStatus
 */
class TransportManagerApplicationStatusTest extends MockeryTestCase
{
    /** @var TransportManagerApplicationStatus */
    private $sut;
    /** @var  m\MockInterface */
    private $mockView;

    /**
     * Setup the view helper
     */
    public function setUp(): void
    {
        $this->mockView = m::mock(RendererInterface::class);

        $this->sut = new TransportManagerApplicationStatus();
        $this->sut->setView($this->mockView);
    }

    public function dataProviderRender()
    {
        return [
            [' orange', RefData::TMA_STATUS_AWAITING_SIGNATURE],
            [' red', RefData::TMA_STATUS_INCOMPLETE],
            [' green', RefData::TMA_STATUS_OPERATOR_SIGNED],
            [' green', RefData::TMA_STATUS_POSTAL_APPLICATION],
            [' orange', RefData::TMA_STATUS_TM_SIGNED],
            [' green', RefData::TMA_STATUS_RECEIVED],
            'invalidStatus' => ['', 'foo'],
        ];
    }

    /**
     * @dataProvider dataProviderRender
     */
    public function testInvoke($expectedClass, $status)
    {
        $this->mockView
            ->shouldReceive('translate')
            ->once()
            ->andReturnUsing(
                function ($desciption) {
                    return '_TRANSL_' . $desciption;
                }
            );

        static::assertEquals(
            '<span class="status' . $expectedClass . '">_TRANSL_' . $status . '</span>',
            $this->sut->__invoke($status, $status)
        );
    }

    public function testRenderDescEmpty()
    {
        $sut = new TransportManagerApplicationStatus();

        static::assertEquals('', $sut->render(null, ''));
    }
}
