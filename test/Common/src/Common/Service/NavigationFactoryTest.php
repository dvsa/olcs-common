<?php

declare(strict_types=1);

namespace CommonTest\Service;

use Common\Service\NavigationFactory;
use Laminas\Navigation\Navigation;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Router\RouteMatch;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;

class NavigationFactoryTest extends TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        $config = [
            'modules'                 => [],
            'module_listener_options' => [
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => [],
                'extra_config'         => [
                    'service_manager' => [
                        'factories' => [
                            'Config' => fn() => [
                                'navigation' => [
                                    'file'    => __DIR__ . '/_files/navigation.xml',
                                    'default' => [
                                        [
                                            'label' => 'Page 1',
                                            'uri'   => 'page1.html'
                                        ],
                                        [
                                            'label' => 'MVC Page',
                                            'route' => 'foo',
                                            'pages' => [
                                                [
                                                    'label' => 'Sub MVC Page',
                                                    'route' => 'foo'
                                                ]
                                            ]
                                        ],
                                        [
                                            'label' => 'Page 3',
                                            'uri'   => 'page3.html'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ],
        ];

        $sm = $this->serviceManager = new ServiceManager();
        $sm->setService('ApplicationConfig', $config);
        $sm->get('ModuleManager')->loadModules();
        $sm->get('Application')->bootstrap();

        $app = $this->serviceManager->get('Application');
        $app->getMvcEvent()->setRouteMatch(
            new RouteMatch(
                [
                    'controller' => 'post',
                    'action'     => 'view',
                    'id'         => '1337',
                ]
            )
        );
    }

    public function testGetNavigation(): void
    {
        $sut = new NavigationFactory($this->serviceManager);
        $this->assertInstanceOf(Navigation::class, $sut->getNavigation([]));
    }

}
