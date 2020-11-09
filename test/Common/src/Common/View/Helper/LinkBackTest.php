<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\LinkBack;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\View\Helper\LinkBack
 */
class LinkBackTest extends MockeryTestCase
{
    /** @var  \Zend\ServiceManager\ServiceManager | m\MockInterface */
    private $mockSm;
    /** @var  m\MockInterface */
    private $mockSl;
    /** @var  m\MockInterface */
    private $mockRequest;

    /** @var  \Zend\View\Renderer\RendererInterface */
    private $mockView;

    /**
     * Setup the view helper
     */
    public function setUp(): void
    {
        $this->mockView = m::mock(\Zend\View\Renderer\RendererInterface::class)
            ->shouldReceive('translate')
            ->zeroOrMoreTimes()
            ->andReturnUsing(
                function ($arg) {
                    return '_TRLTD_' . $arg;
                }
            )
            ->getMock();

        $this->mockRequest = m::mock(\Zend\Http\Request::class);

        $this->mockSl = m::mock(\Zend\ServiceManager\ServiceManager::class)
            ->shouldReceive('get')->once()->with('Request')->andReturn($this->mockRequest)
            ->getMock();

        $this->mockSm = m::mock(\Zend\View\HelperPluginManager::class)
            ->shouldReceive('getServiceLocator')->once()->andReturn($this->mockSl)
            ->getMock();
    }

    /**
     * @dataProvider dpTestInvoke
     */
    public function testInvoke($params, $referer, $expect)
    {
        if ($referer !== null) {
            $this->mockRequest->shouldReceive('getHeader')->once()->with('referer')->andReturn($referer);
        }

        $sut = (new LinkBack())
            ->createService($this->mockSm)
            ->setView($this->mockView);

        static::assertEquals($expect, $sut->__invoke($params));
    }

    public function dpTestInvoke()
    {
        $mockHeader = m::mock(\Zend\Http\Header\HeaderInterface::class);
        $mockHeader->shouldReceive('uri->getPath')->atMost(1)->andReturn('unit_URL');

        return [
            //  parameter not set, no referer page
            [
                'params' => null,
                'referer' => false,
                'expect' => '',
            ],
            //  parameter not set, has referer page
            [
                'params' => null,
                'referer' => $mockHeader,
                'expect' => '<a href="unit_URL" class="govuk-back-link">_TRLTD_common.link.back.label</a>',
            ],
            //  parameter not set, has referer page
            [
                'params' => [
                    'label' => 'unit_PrmLbl',
                    'url' => 'unit_PrmUrl2',
                    'escape' => false,
                ],
                'referer' => null,
                'expect' => '<a href="unit_PrmUrl2" class="govuk-back-link">_TRLTD_unit_PrmLbl</a>',
            ],
        ];
    }
}
