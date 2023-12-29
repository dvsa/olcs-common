<?php

namespace CommonTest\Common\Rbac\Navigation;

use Common\Rbac\Navigation\IsAllowedListener;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Mvc\MvcEvent;
use Laminas\Navigation;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * @covers \Common\Rbac\Navigation\IsAllowedListener
 */
class IsAllowedListenerTest extends MockeryTestCase
{
    /** @var  m\MockInterface|ModuleOptions */
    private $mockModuleOptions;
    /** @var  m\MockInterface|AuthorizationService */
    private $mockAuthSrv;

    public function setUp(): void
    {
        $this->mockModuleOptions = m::mock(ModuleOptions::class);
        $this->mockAuthSrv = m::mock(AuthorizationService::class);
    }

    public function testAcceptNotMvcPage()
    {
        $mockPage = m::mock(Navigation\Page\AbstractPage::class);

        /** @var m\MockInterface|MvcEvent $mockEvent */
        $mockEvent = m::mock(MvcEvent::class)
            ->shouldReceive('getParam')
            ->once()
            ->with('page')
            ->andReturn($mockPage)
            ->getMock();

        $sut = new IsAllowedListener();
        static::assertTrue($sut->accept($mockEvent));
    }

    public function testAcceptOk()
    {
        $mockPage = m::mock(Navigation\Page\Mvc::class)->makePartial();

        /** @var m\MockInterface|MvcEvent $mockEvent */
        $mockEvent = m::mock(MvcEvent::class)
            ->shouldReceive('getParam')
            ->once()
            ->with('page')
            ->andReturn($mockPage)
            //
            ->shouldReceive('stopPropagation')
            ->once()
            ->withNoArgs()
            ->getMock();

        $sut = new IsAllowedListener();
        static::assertFalse($sut->accept($mockEvent));
    }

    /**
     * @dataProvider  dataProviderTestIsGranted
     */
    public function testIsGranted($route, $rules, $policy, $isGranted, $expect)
    {
        if ($isGranted !== null) {
            $this->mockAuthSrv
                ->shouldReceive('isGranted')
                ->once()
                ->with('unit_Permission')
                ->andReturn($isGranted);
        }

        $this->mockModuleOptions
            ->shouldReceive('getProtectionPolicy')
            ->once()
            ->andReturn($policy)
            //
            ->shouldReceive('getGuards')
            ->andReturn(
                [
                    'LmcRbacMvc\Guard\RoutePermissionsGuard' => $rules,
                ]
            )
            ->getMock();

        /** @var Navigation\Page\Mvc $mockPage */
        $mockPage = m::mock(Navigation\Page\Mvc::class)->makePartial()
            ->shouldReceive('getRoute')
            ->once()
            ->andReturn($route)
            ->getMock();

        $sut = (new IsAllowedListener())
            ->__invoke(
                $this->mockServiceLocator(),
                IsAllowedListener::class
            );

        static::assertEquals($expect, $sut->isGranted($mockPage));
    }

    public function dataProviderTestIsGranted()
    {
        return [
            //  rules not presented for route, policy DENY
            [
                'route' => 'unit_Route',
                'rules' => [
                    'unit_RouteOther' => [],
                ],
                'policy' => GuardInterface::POLICY_DENY,
                'isGranted' => null,
                'expect' => false,
            ],
            //  rules presented with '*'
            [
                'route' => 'unit_Route',
                'rules' => [
                    'unit_Route' => ['*'],
                ],
                'policy' => null,
                'isGranted' => null,
                'expect' => true,
            ],
            //  rules is empty
            [
                'route' => 'unit_Route',
                'rules' => [
                    'unit_Route' => [],
                ],
                'policy' => null,
                'isGranted' => null,
                'expect' => true,
            ],
            //  rules has permission, but not Granted
            [
                'route' => 'unit_Route',
                'rules' => [
                    'unit_Route' => ['unit_Permission'],
                ],
                'policy' => null,
                'isGranted' => false,
                'expect' => false,
            ],
        ];
    }

    /**
     * @return m\MockInterface|ContainerInterface
     */
    private function mockServiceLocator()
    {
        $closure = function ($class) {
            $map = [
                AuthorizationService::class => $this->mockAuthSrv,
                ModuleOptions::class => $this->mockModuleOptions,
            ];

            return $map[$class];
        };

        return m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing($closure)
            ->getMock();
    }
}
