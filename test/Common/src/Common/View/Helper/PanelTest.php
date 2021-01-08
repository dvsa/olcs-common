<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\Panel;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\View\Helper\LinkBack
 */
class PanelTest extends MockeryTestCase
{
    /** @var  \Laminas\View\Renderer\RendererInterface */
    private $mockView;

    /**
     * @dataProvider dpTestInvoke
     */
    public function testInvoke($params, $expect, $expected_css_class)
    {
        $this->mockView = m::mock(\Laminas\View\Renderer\RendererInterface::class)
            ->shouldReceive('render')
            ->withArgs([
                'partials/panel',
                [
                    'theme' => $expected_css_class,
                    'title' => $params['title'],
                    'body' => $params['body']
                ]
            ])
            ->Once()
            ->andReturn('html_string')
            ->getMock();

        $sut = (new Panel())
            ->setView($this->mockView);

        static::assertEquals($expect, $sut->__invoke($params['type'], $params['title'], $params['body']));
    }

    public function dpTestInvoke()
    {
        return [
            //  parameter not set, no referer page
            [
                'params' => [
                    'type' => 'success',
                    'title' => 'title',
                    'body' => 'body',
                ],
                'expect' => 'html_string',
                'expected_css_class' => 'govuk-panel--confirmation'
            ],
            [
                'params' => [
                    'type' => 'does not exist',
                    'title' => 'title',
                    'body' => 'body',
                ],
                'expect' => 'html_string',
                'expected_css_class' => ''
            ],
        ];
    }
}
