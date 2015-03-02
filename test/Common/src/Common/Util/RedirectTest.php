<?php

/**
 * Redirect Util Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Util;

use Common\Util\Redirect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Redirect Util Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RedirectTest extends MockeryTestCase
{
    protected $sut;
    protected $redirectPlugin;

    public function setUp()
    {
        $this->sut = new Redirect();
        $this->redirectPlugin = m::mock();
    }

    public function testToRoute()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);

        $this->sut->toRoute('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);
        $this->sut->process($this->redirectPlugin);
    }

    public function testToRouteDefaults()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with(null, [], [], false);

        $this->sut->toRoute();
        $this->sut->process($this->redirectPlugin);
    }

    public function testToRouteAjax()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);

        $this->sut->toRouteAjax('foo', ['foo' => 'bar'], ['cake' => 'bar'], true);
        $this->sut->process($this->redirectPlugin);
    }

    public function testToRouteAjaxDefaults()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with(null, [], [], false);

        $this->sut->toRouteAjax();
        $this->sut->process($this->redirectPlugin);
    }

    public function testRefresh()
    {
        $this->redirectPlugin->shouldReceive('toRoute')
            ->with(null, [], [], true);

        $this->sut->refresh();
        $this->sut->process($this->redirectPlugin);
    }

    public function testRefreshAjax()
    {
        $this->redirectPlugin->shouldReceive('toRouteAjax')
            ->with(null, [], [], true);

        $this->sut->refreshAjax();
        $this->sut->process($this->redirectPlugin);
    }
}
