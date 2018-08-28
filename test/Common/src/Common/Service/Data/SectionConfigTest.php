<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\SectionConfig;
use Common\Util\LvaRoute;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\Router\Http\Segment;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Section Config Test
 *
 * @package CommonTest\Service\Data
 */
class SectionConfigTest extends MockeryTestCase
{
    public function testGetAll()
    {
        $sut = new SectionConfig();

        /** @var m\Mock|ServiceLocatorInterface $sm */
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('Processing\VariationSection')
            ->getMock();
        $sut->setServiceLocator($sm);

        $all = $sut->getAll();

        $totalSections = count($all);

        // undertakings sections should have all sections bar itself as a prerequisite
        $this->assertEquals(
            ($totalSections - 1),
            count($all['undertakings']['prerequisite'][0])
        );
    }

    public function testGetAllRoutes()
    {
        $sections = [
            'unit_test_route' => [],
            'unit_second_route' => [],
        ];

        /** @var SectionConfig | m\MockInterface $sut */
        $sut = m::mock(SectionConfig::class)->makePartial();
        $sut->shouldReceive('getAllReferences')->once()->andReturn(array_keys($sections));

        $actual = $sut->getAllRoutes();

        static::assertEquals(
            [
                'lva-application' => [
                    'type' => 'segment',
                    'options' => [
                        'route' => '/application/:application[/]',
                        'constraints' => [
                            'application' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => 'LvaApplication',
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [] +
                        $this->getTestChildRoute('unit_test_route', 'application') +
                        $this->getTestChildRoute('unit_second_route', 'application'),
                ],
                'lva-licence' => [
                    'type' => 'segment',
                    'options' => [
                        'route' => '/licence/:licence[/]',
                        'constraints' => [
                            'licence' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => 'LvaLicence',
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [] +
                        $this->getTestChildRoute('unit_test_route', 'licence') +
                        $this->getTestChildRoute('unit_second_route', 'licence'),
                ],
                'lva-variation' => [
                    'type' => 'segment',
                    'options' => [
                        'route' => '/variation/:application[/]',
                        'constraints' => [
                            'application' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => 'LvaVariation',
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [] +
                        $this->getTestChildRoute('unit_test_route', 'variation') +
                        $this->getTestChildRoute('unit_second_route', 'variation'),
                ],
                'lva-director_change' => [
                    'type' => 'segment',
                    'options' => [
                        'route' => '/director-change/:application[/]',
                        'constraints' => [
                            'application' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => 'LvaDirectorChange',
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [] +
                        $this->getTestChildRoute('unit_test_route', 'director_change') +
                        $this->getTestChildRoute('unit_second_route', 'director_change'),
                ],
                'lva-transport_manager' => [
                    'type' => 'segment',
                    'options' => [
                        'route' => '/transport-manager/:application[/]',
                        'constraints' => [
                            'application' => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => 'LvaTransportManager',
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [] +
                        $this->getTestChildRoute('unit_test_route', 'transport_manager') +
                        $this->getTestChildRoute('unit_second_route', 'transport_manager'),
                ],

            ],
            $actual
        );
    }

    private function getTestChildRoute($key, $parent)
    {
        $route = str_replace('_', '-', $key);
        $controller = sprintf(
            'Lva%s/%s',
            str_replace(' ', '', ucwords(str_replace('_', ' ', $parent))),
            str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))
        );

        return [
            $key => [
                'type' => LvaRoute::class,
                'options' => [
                    'route' => $route . '[/]',
                    'defaults' => [
                        'controller' => $controller,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'action' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => ':action[/:child_id][/]',
                        ],
                    ],
                ],
            ],
        ];
    }
}
