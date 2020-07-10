<?php

namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\ReadOnlyActions;
use Zend\I18n\View\Helper\Translate;
use Zend\View\Renderer\RendererInterface;

/**
 * ReadOnlyActions Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReadOnlyActionsTest extends MockeryTestCase
{
    const BUTTON_LAYOUT = '<input type="submit" name="action" id="%s" class="%s" value="%s">';

    /**
     * @var ReadOnlyActions
     */
    private $sut;

    private $wrapper = '<div class="actions-container">%s</div>';

    private $mockView;

    public function setUp(): void
    {
        $this->mockView = m::mock(RendererInterface::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($text) {
                    return $text . '-translated';
                }
            )
            ->getMock();

        $this->sut = new ReadOnlyActions();
        $this->sut->setView($this->mockView);
    }

    public function testInvokeWithUrl()
    {
        $url = 'http://foo.com';
        $label = 'Bar';
        $class = 'large';
        $actions = [
            [
                'url'   => $url,
                'class' => $class,
                'label' => $label,
                'attributes' => [
                    'key' => 'val'
                ]
            ]
        ];

        $expected = sprintf(
            ReadOnlyActions::LINK_WRAPPER,
            $url,
            $class,
            'key="val"',
            $label . '-translated'
        );
        $markup = sprintf(ReadOnlyActions::WRAPPER, $expected);
        $this->assertEquals($markup, $this->sut->__invoke($actions));
    }

    public function testInvokeWithoutUrl()
    {
        $label = 'Bar';
        $class = 'large';
        $actions = [
            [
                'class' => $class,
                'label' => $label,
            ]
        ];
        $expected = sprintf(self::BUTTON_LAYOUT, strtolower($label), $class, $label);
        $this->mockView
            ->shouldReceive('formInput')
            ->andReturn($expected)
            ->once()
            ->getMock();

        $markup = sprintf(ReadOnlyActions::WRAPPER, $expected);
        $this->assertEquals($markup, $this->sut->__invoke($actions));
    }
}
