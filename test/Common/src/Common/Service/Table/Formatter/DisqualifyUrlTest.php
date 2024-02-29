<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\DisqualifyUrl;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Disqualify Url formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DisqualifyUrlTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $router;
    protected $request;
    protected $authService;
    protected $mockRouteMatch;
    protected $sut;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->router = m::mock(TreeRouteStack::class);
        $this->request = m::mock(Request::class);
        $this->authService = m::mock(AuthorizationService::class);
        $this->mockRouteMatch = m::mock(\Laminas\Router\RouteMatch::class);
        $this->sut = new DisqualifyUrl($this->urlHelper, $this->router, $this->request, $this->authService);

        $this->request->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                    ->shouldReceive('toArray')
                    ->once()
                    ->andReturn(['foo' => 'bar'])
                    ->getMock()
            )
            ->once();

        $this->router->shouldReceive('match')
            ->andReturn($this->mockRouteMatch);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $routeMatch, $expectedRoute, $expectedRouteParams, $params, $expectedLink)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($routeMatch)
            ->once()
            ->shouldReceive('getParams')
            ->andReturn($params)
            ->once();

        if ($expectedRoute !== null) {
            $this->urlHelper
                ->shouldReceive('fromRoute')
                ->with($expectedRoute, $expectedRouteParams, ['query' => ['foo' => 'bar']], true)
                ->andReturn('the_url');
        }

        $this->assertEquals($expectedLink, $this->sut->format($data, []));
    }

    public function provider()
    {
        return [
            'licence' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-licence/people',
                'disqualify-person/licence',
                ['person' => '99', 'licence' => 1],
                ['licence' => 1],
                '<a href="the_url" class="govuk-link js-modal-ajax">foo</a>',
            ],
            'application' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-application/people',
                'disqualify-person/application',
                ['person' => '99', 'application' => 2],
                ['application' => 2],
                '<a href="the_url" class="govuk-link js-modal-ajax">foo</a>',
            ],
            'variation' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-variation/people',
                'disqualify-person/variation',
                ['person' => '99', 'variation' => 3],
                ['application' => 3],
                '<a href="the_url" class="govuk-link js-modal-ajax">foo</a>',
            ],
            'unknown' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'bar',
                null,
                null,
                null,
                '<a href="" class="govuk-link js-modal-ajax">foo</a>',
            ],
        ];
    }
}
