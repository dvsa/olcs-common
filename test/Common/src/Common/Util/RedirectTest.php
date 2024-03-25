<?php

declare(strict_types=1);

namespace CommonTest\Util;

use Common\Util\Redirect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RedirectTest extends MockeryTestCase
{
    protected $sut;
    protected $redirectPlugin;

    public function setUp(): void
    {
        $this->sut = new Redirect();
        $this->redirectPlugin = m::mock();
    }

    public function testToRoute()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with('foo', ['foo' => 'bar'], ['cake' => 'bar'], true)->andReturnSelf();

        $this->sut->toRoute('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }

    public function testToRouteDefaults()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with(null, [], [], false)->andReturnSelf();

        $this->sut->toRoute();
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }

    public function testToRouteAjax()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with('foo', ['foo' => 'bar'], ['cake' => 'bar'], true)->andReturnSelf();

        $this->sut->toRouteAjax('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }

    public function testToRouteAjaxDefaults()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with(null, [], [], false)->andReturnSelf();

        $this->sut->toRouteAjax();
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }

    public function testRefresh()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with(null, [], [], true)->andReturnSelf();

        $this->sut->refresh();
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }

    public function testRefreshAjax()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with(null, [], [], true)->andReturnSelf();

        $this->sut->refreshAjax();
        $this->assertEquals($this->redirectPlugin, $this->sut->process($this->redirectPlugin));
    }
}
