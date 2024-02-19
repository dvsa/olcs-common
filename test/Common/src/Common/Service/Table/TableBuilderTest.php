<?php

namespace CommonTest\Service\Table;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\ContentHelper;
use Common\Service\Table\Exception\MissingFormatterException;
use Common\Service\Table\Formatter\Date;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Common\Service\Table\PaginationHelper;
use Common\Service\Table\TableBuilder;
use Hamcrest\Arrays\IsArrayContainingKey;
use Hamcrest\Arrays\IsArrayContainingKeyValuePair;
use Hamcrest\Core\IsAnything;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\Mvc\I18n\Translator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * @covers \Common\Service\Table\TableBuilder
 */
class TableBuilderTest extends MockeryTestCase
{
    private const TRANSLATED = '_TRSLTD_';

    private $mockFormatterPluginManager;

    public function setup(): void
    {
        $this->mockFormatterPluginManager = m::mock(FormatterPluginManager::class);
    }

    private function getConcreteTableBuilder($config = true)
    {
        return new TableBuilder(
            $this->getMockServiceLocator(),
            $this->getMockAuthService(),
            $this->getMockTranslator(),
            $this->getMockUrlHelperService(),
            $this->getMockConfig($config),
            $this->mockFormatterPluginManager
        );
    }

    /**
     * Get Mock Table Builder
     *
     * @return \Common\Service\Table\TableBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockTableBuilder($methods = [], $constructorArgs = null)
    {
        if (is_null($constructorArgs)) {
            $constructorArgs = [
                $this->getMockServiceLocator(),
                $this->getMockAuthService(),
                $this->getMockTranslator(),
                $this->getMockUrlHelperService(),
                $this->getMockConfig(),
                $this->mockFormatterPluginManager
            ];
        }

        return $this->getMockBuilder(TableBuilder::class)
            ->setMethods($methods)
            ->setConstructorArgs($constructorArgs)
            ->getMock();
    }

    private function getMockTranslator()
    {
        $mockTranslator = $this->createPartialMock(\Laminas\Mvc\I18n\Translator::class, ['translate']);
        $mockTranslator->expects(static::any())
            ->method('translate')
            ->willReturnCallback(
                function ($desc) {
                    if (!is_string($desc)) {
                        return $desc;
                    }

                    return self::TRANSLATED . $desc;
                }
            );

        return $mockTranslator;
    }

    private function getMockAuthService()
    {
        return $this->createPartialMock(AuthorizationService::class, ['isGranted']);
    }

    private function getMockUrlHelperService()
    {
        return m::mock(UrlHelperService::class);
    }

    private function getMockConfig($config = true)
    {
        if (!$config) {
            return [];
        }

        return [
            'tables' => [
                'config' => [__DIR__ . '/TestResources/'],
                'partials' => [
                    'html' => ''
                ]
            ],
            'csrf' => [
                'timeout' => 9999,
            ],
        ];
    }

    private function getMockServiceLocator()
    {
        $mockSm = $this->createPartialMock('\Laminas\ServiceManager\ServiceManager', ['get']);
        $mockControllerPluginManager = $this->createPartialMock('\Laminas\Mvc\Controller\PluginManager', ['get']);

        $servicesMap = [
            ['ControllerPluginManager', true, $mockControllerPluginManager],
        ];

        $mockSm
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($servicesMap));

        return $mockSm;
    }

    /**
     * Test getContentHelper
     */
    public function testGetContentHelper()
    {
        $table = $this->getConcreteTableBuilder();

        $contentHelper = $table->getContentHelper();

        $this->assertTrue($contentHelper instanceof \Common\Service\Table\ContentHelper);

        $contentHelper2 = $table->getContentHelper();

        $this->assertTrue($contentHelper === $contentHelper2);
    }

    /**
     * Test getContentHelper without configured partials
     *
     */
    public function testGetContentHelperWithoutConfig()
    {
        $this->expectException(\Exception::class);

        $table = $this->getConcreteTableBuilder(false);

        $table->getContentHelper();
    }

    /**
     * Test getContentHelper without configured partials for current content type
     *
     */
    public function testGetContentHelperWithoutConfigForType()
    {
        $this->expectException(\Exception::class);

        $table = $this->getConcreteTableBuilder();

        $table->setContentType('csv');

        $table->getContentHelper();
    }

    /**
     * Test getPaginationHelper
     */
    public function testGetPaginationHelper()
    {
        $table = $this->getConcreteTableBuilder();

        $paginationHelper = $table->getPaginationHelper();

        $this->assertTrue($paginationHelper instanceof \Common\Service\Table\PaginationHelper);

        $paginationHelper2 = $table->getPaginationHelper();

        $this->assertTrue($paginationHelper === $paginationHelper2);
    }

    /**
     * Test getConfigFromFile
     */
    public function testGetConfigFromFile()
    {
        $table = $this->getConcreteTableBuilder();

        $config = $table->getConfigFromFile('sample');

        $this->assertEquals(['foo' => 'bar'], $config);
    }

    /**
     * Test getConfigFromFile with missing file
     *
     */
    public function testGetConfigFromFileWithMissingFile()
    {
        $this->expectException(\Exception::class);

        $table = $this->getConcreteTableBuilder();

        $table->getConfigFromFile('DoesntExist');
    }

    /**
     * Test build table calls all of the relevant methods
     */
    public function testBuildTable()
    {
        $table = m::mock(TableBuilder::class)->makePartial();

        $table->expects('loadConfig');
        $table->expects('loadData');
        $table->expects('loadParams');
        $table->expects('setupAction');
        $table->expects('render')->andReturn('SomeHTML');

        $this->assertEquals('SomeHTML', $table->buildTable('test'));
    }

    /**
     * Test build table without render
     */
    public function testBuildTableWithoutRender()
    {
        $table = m::mock(TableBuilder::class)->makePartial();

        $table->expects('loadConfig');
        $table->expects('loadData');
        $table->expects('loadParams');
        $table->expects('setupAction');

        $this->assertEquals($table, $table->buildTable('test', [], [], false));
    }

    /**
     * Test loadConfig without table config set
     *
     */
    public function testLoadConfigWithoutTableConfig()
    {
        $this->expectException(\Exception::class);

        $table = $this->getConcreteTableBuilder(false);

        $table->loadConfig('test');
    }

    /**
     * Test loadConfig with empty array
     */
    public function testLoadConfigWithEmptyArray()
    {
        $tableConfig = [];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $this->assertTrue($table->loadConfig('test'));

        $this->assertEquals(null, $table->getSetting('paginate'));

        $this->assertEquals('default', $table->getSetting('paginate', 'default'));

        $this->assertEquals(['class' => TableBuilder::CLASS_TABLE], $table->getAttributes());
        $this->assertEquals([], $table->getColumns());
        $this->assertEquals(['hidden' => 'default'], $table->getVariables());
    }

    /**
     * Test loadConfig With pagination settings With limit
     */
    public function testLoadConfigWithPaginationWithLimit()
    {
        $paginate = [
            'limit' => [
                'default' => 20,
                'options' => [
                    5, 10, 20
                ]
            ]
        ];

        $tableConfig = [
            'settings' => [
                'paginate' => $paginate
            ]
        ];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $this->assertTrue($table->loadConfig('test'));

        $this->assertEquals($paginate, $table->getSetting('paginate'));
    }

    /**
     * Test loadConfig With pagination settings Without limit
     */
    public function testLoadConfigWithPaginationWithoutLimit()
    {
        $paginate = [
            'limit' => [
                'default' => 10,
                'options' => [
                    10, 25, 50
                ]
            ]
        ];

        $tableConfig = [
            'settings' => [
                'paginate' => []
            ]
        ];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $this->assertTrue($table->loadConfig('test'));

        $this->assertEquals($paginate, $table->getSetting('paginate'));
    }

    /**
     * Test loadConfig With action field name
     */
    public function testLoadConfigWithActionFieldNameAndFormName()
    {
        $paginate = [
            'limit' => [
                'default' => 10,
                'options' => [
                    10, 25, 50
                ]
            ]
        ];

        $tableConfig = [
            'settings' => [
                'paginate' => [],
                'crud' => [
                    'formName' => 'bob',
                    'action_field_name' => 'blah'
                ]
            ]
        ];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $this->assertTrue($table->loadConfig('test'));

        $this->assertEquals($paginate, $table->getSetting('paginate'));
    }

    /**
     * Test loadData without data
     */
    public function testLoadDataWithoutData()
    {
        $data = [];

        $table = $this->getConcreteTableBuilder();

        $table->loadData($data);

        $this->assertEquals([], $table->getRows());
        $this->assertFalse($table->hasRows());

        $this->assertEquals(0, $table->getTotal());
    }

    /**
     * Test loadData with rows of data
     */
    public function testLoadDataWithDataRows()
    {
        $data = [
            ['foo' => 'bar'],
            ['foo' => 'bar']
        ];

        $table = $this->getConcreteTableBuilder();

        $table->loadData($data);

        $this->assertEquals($data, $table->getRows());
        $this->assertTrue($table->hasRows());

        $this->assertEquals(2, $table->getTotal());
    }

    /**
     * Test loadData with 1 row of data
     */
    public function testLoadDataWithOneRow()
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $table = $this->getConcreteTableBuilder();

        $table->setVariable('title', 'Things');
        $table->setVariable('titleSingular', 'Thing');

        $table->loadData($data);

        $this->assertEquals($data, $table->getRows());
        $this->assertTrue($table->hasRows());

        $this->assertEquals(1, $table->getTotal());
        self::assertEquals(self::TRANSLATED . 'Thing', $table->getVariable('title'));
    }

    /**
     * Test loadData with result data
     */
    public function testLoadDataWithResultData()
    {
        $rows = [
            ['foo' => 'bar'],
            ['foo' => 'bar']
        ];

        $data = [
            'Results' => $rows,
            'Count' => 10
        ];

        $table = $this->getConcreteTableBuilder();

        $table->loadData($data);

        $this->assertEquals($rows, $table->getRows());

        $this->assertEquals(10, $table->getTotal());
    }

    /**
     * Test loadParams Without Url
     *
     * @doesNotPerformAssertions
     */
    public function testLoadParamsWithoutUrl()
    {
        $params = [];

        $table = $this->getConcreteTableBuilder();

        $table->loadParams($params);
    }

    /**
     * Test loadParams With limit
     */
    public function testLoadParamsWithLimit()
    {
        $url = new \stdClass();

        $params = [
            'url' => $url,
            'limit' => 10
        ];

        $expected = array_merge(['page' => 1, 'sort' => '', 'order' => 'ASC'], $params);

        $table = $this->getConcreteTableBuilder();

        $table->loadParams($params);

        $this->assertSame($url, $table->getUrl());

        $this->assertEquals(10, $table->getLimit());
        $this->assertEquals('', $table->getSort());
        $this->assertEquals('ASC', $table->getOrder());
        $this->assertEquals($expected, $table->getVariables());
    }

    /**
     * Test loadParams With default limit
     */
    public function testLoadParamsWithDefaultLimit()
    {
        $url = new \stdClass();

        $params = [
            'url' => $url
        ];

        $tableConfig = [
            'variables' => [
                'foo' => 'bar',
                'title' => 'Test',
            ],
            'settings' => [
                'paginate' => []
            ]
        ];

        $expectedVariables = $params;

        $expectedVariables['foo'] = 'bar';
        $expectedVariables['title'] = self::TRANSLATED . 'Test';

        $expectedVariables['hidden'] = 'default';
        $expectedVariables['limit'] = 10;
        $expectedVariables['page'] = 1;
        $expectedVariables['sort'] = '';
        $expectedVariables['order'] = 'ASC';

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $table->loadConfig('test');

        $table->loadParams($params);

        $this->assertSame($url, $table->getUrl());

        $this->assertEquals(10, $table->getLimit());
        $this->assertEquals('', $table->getSort());
        $this->assertEquals('ASC', $table->getOrder());
        $this->assertEquals($expectedVariables, $table->getVariables());
    }

    /**
     * Test loadParams With Query
     */
    public function testLoadParamsWithQuery()
    {
        $query = new \stdClass();

        $params = [
            'url' => 'foo',
            'query' => $query,
        ];

        $table = $this->getConcreteTableBuilder();

        $table->loadParams($params);

        $this->assertSame($query, $table->getQuery());
    }

    /**
     * Test setupAction with action set
     */
    public function testSetupActionWithActionSet()
    {
        $variables = [
            'action' => '/'
        ];

        $table = $this->getMockTableBuilder(['getVariables', 'getUrl']);

        $table->expects($this->any())
            ->method('getVariables')
            ->will($this->returnValue($variables));

        $table->setupAction();

        $table->expects($this->never())
            ->method('getUrl');
    }

    /**
     * Test setupAction without action set
     */
    public function testSetupActionWithoutActionSet()
    {
        $variables = [];

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('/someaction'));

        $table = $this->getMockTableBuilder(['getVariables', 'getUrl']);

        $table->expects($this->any())
            ->method('getVariables')
            ->will($this->returnValue($variables));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->setupAction();
    }

    /**
     * Test setupAction with action_route set
     */
    public function testSetupActionWithActionRouteSet()
    {
        $variables = [
            'action_route' => ['route' => 'someroute', 'params' => ['foo' => 'bar']]
        ];

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('/someaction'));

        $table = $this->getMockTableBuilder(['getVariables', 'getUrl']);

        $table->expects($this->any())
            ->method('getVariables')
            ->will($this->returnValue($variables));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->setupAction();
    }

    /**
     * Test render
     */
    public function testRender()
    {

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('HTML', [])
            ->will($this->returnValue('MORE HTML'));

        $table = $this->getMockTableBuilder(['renderTable', 'getVariables', 'getContentHelper']);

        $table->expects($this->once())
            ->method('renderTable')
            ->will($this->returnValue('HTML'));

        $table->expects($this->once())
            ->method('getVariables')
            ->will($this->returnValue([]));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('MORE HTML', $table->render());
    }

    /**
     * Test renderTableFooter without footer
     */
    public function testRenderTableFooterWithoutFooter()
    {
        $table = $this->getConcreteTableBuilder();

        $this->assertEquals('', $table->renderTableFooter());
    }

    /**
     * Test renderTableFooter
     */
    public function testRenderTableFooter()
    {
        $footer = [
            [
                'type' => 'th',
                'colspan' => 2,
                'content' => 'foo',
                'formatter' => function () {
                    return 'ABC';
                },
                'align' => 'right',
            ],
            [
                'format' => 'HTML'
            ]
        ];

        $table = $this->getMockTableBuilder(
            [
                'getContentHelper',
            ]
        );

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return $string;
                    }
                )
            );

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setFooter($footer);

        $this->assertEquals('{{[elements/tableFooter]}}', $table->renderTableFooter());
    }

    /**
     * Test renderTable For Hybrid
     */
    public function testRenderTableForHybrid()
    {
        $settings = [
            'crud' => 'foo',
            'paginate' => 'bar'
        ];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_HYBRID);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('crud');

        $table->setSettings($settings);

        $table->renderTable();
    }

    /**
     * Test renderTable For Crud
     */
    public function testRenderTableForCrud()
    {
        $settings = [
            'crud' => 'foo'
        ];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_CRUD);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('crud');

        $table->setSettings($settings);

        $table->renderTable();

        $csrfElm = $table->getCsrfElement();
        static::assertEquals('security', $csrfElm->getName());
        static::assertEquals(
            [
                'csrf_options' => [
                    'timeout' => 9999,
                ],
            ],
            $csrfElm->getOptions()
        );
    }

    /**
     * Test renderTable For SubmissionSection
     */
    public function testRenderTableForSubmissionSection()
    {
        $settings = [
            'submission_section' => 'foo'
        ];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_DEFAULT);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('submission-section');

        $table->setSettings($settings);

        $table->renderTable();
    }

    /**
     * Test renderTable For Crud within form
     */
    public function testRenderTableForCrudWithinForm()
    {
        $settings = [
            'crud' => 'foo'
        ];

        $variables = [
            'within_form' => true
        ];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_FORM_TABLE);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('default');

        $table->setSettings($settings);

        $table->setVariables($variables);

        $table->renderTable();
    }

    /**
     * Test renderTable For pagination
     */
    public function testRenderTableForPagination()
    {
        $settings = [
            'paginate' => 'foo'
        ];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_PAGINATE);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('default');

        $table->setSettings($settings);

        $table->renderTable();
    }

    /**
     * Test renderTable default
     */
    public function testRenderTableDefault()
    {
        $settings = [];

        $table = $this->getMockTableBuilder(['setType', 'renderLayout']);

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_DEFAULT);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('default');

        $table->setSettings($settings);

        $table->renderTable();
    }

    /**
     * Test renderLayout
     */
    public function testRenderLayout()
    {
        $name = 'foo';

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['renderLayout']);

        $mockContentHelper->expects($this->once())
            ->method('renderLayout')
            ->with($name)
            ->will($this->returnValue($name));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals($name, $table->renderLayout($name));
    }

    /**
     * Test renderTotal Without pagination
     */
    public function testRenderTotalWithoutPagination()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setType(TableBuilder::TYPE_CRUD);

        $this->assertEquals('', $table->renderTotal());
    }

    /**
     * Test renderTotal With pagination
     */
    public function testRenderTotalWithPagination()
    {
        $total = 10;

        $expectedTotal = 10;

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(' {{[elements/total]}}', ['total' => $expectedTotal])
            ->will($this->returnValue($expectedTotal));

        $table = $this->getMockTableBuilder(['getContentHelper', 'shouldPaginate']);

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->once())
            ->method('shouldPaginate')
            ->will($this->returnValue(true));

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderTotal With pagination With 1 result
     */
    public function testRenderTotalWithPaginationWith1()
    {
        $total = 1;

        $expectedTotal = 1;

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(' {{[elements/total]}}', ['total' => $expectedTotal])
            ->will($this->returnValue($expectedTotal));

        $table = $this->getMockTableBuilder(['getContentHelper', 'shouldPaginate']);

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->once())
            ->method('shouldPaginate')
            ->will($this->returnValue(true));

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderTotal Without pagination but with showTotal setting
     */
    public function testRenderTotalWithoutPaginationWithShowTotal()
    {
        $total = 10;
        $expectedTotal = 10;

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(' {{[elements/total]}}', ['total' => $expectedTotal])
            ->will($this->returnValue($expectedTotal));

        $table = m::mock(TableBuilder::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $table->expects('getContentHelper')->andReturn($mockContentHelper);
        $table->expects('shouldPaginate')->andReturnFalse();
        $table->expects('getSetting')->with('overrideTotal', false)->andReturnFalse();
        $table->expects('getSetting')->with('showTotal', false)->andReturnTrue();

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderTotal override
     */
    public function testRenderTotalWithOverride()
    {
        $total = 10;
        $expectedTotal = '';

        $table = $this->getMockTableBuilder(['getSetting']);

        $table->expects($this->once())
            ->method('getSetting')
            ->with('overrideTotal', false)
            ->will($this->returnValue(true));

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderActions With Pagination
     */
    public function testRenderActionsWithoutCrud()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $this->assertEquals('', $table->renderActions());
    }

    /**
     * Test renderActions without actions
     */
    public function testRenderActionsWithoutActions()
    {
        $settings = [
            'crud' => []
        ];

        $table = $this->getConcreteTableBuilder();

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals('', $table->renderActions());
    }

    public function testTrimActionsHaveRows()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'action_1' => [
                        'requireRows' => true,
                    ],
                    'action_2' => [
                        'requireRows' => false,
                    ],
                ],
            ]
        ];

        $mockContentHelper = m::mock(\Common\Service\Table\ContentHelper::class);
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->times(2)
            ->with('{{[elements/actionButton]}}', m::any())
            ->andReturnUsing(
                function ($content, $details) {
                    return $details['name'];
                }
            );
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->once()
            ->with(
                '{{[elements/actionContainer]}}',
                [
                    'content' => 'action_1action_2',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);
        $table
            ->setType(TableBuilder::TYPE_CRUD)
            ->setRows(['HAVE ROWS'])
            ->setSettings($settings);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->willReturn($mockContentHelper);

        $table->renderActions();
    }

    public function testTrimActionsReadOnlyUser()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'action_1' => [
                    ],
                    'action_3' => [
                        'keepForReadOnly' => true,
                    ],
                    'action_4' => [
                        'keepForReadOnly' => false,
                    ],
                ],
            ]
        ];

        $mockContentHelper = m::mock(\Common\Service\Table\ContentHelper::class);
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->with('{{[elements/actionButton]}}', m::any())
            ->andReturnUsing(
                function ($content, $details) {
                    return $details['name'];
                }
            );
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->once()
            ->with(
                '{{[elements/actionContainer]}}',
                [
                    'content' => 'action_3',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'isInternalReadOnly']);
        $table
            ->setType(TableBuilder::TYPE_CRUD)
            ->setRows([])
            ->setSettings($settings);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->willReturn($mockContentHelper);

        $table->expects($this->exactly(2))
            ->method('isInternalReadOnly')
            ->willReturn(true);

        $table->renderActions();
    }

    public function testTrimActionsNoRows()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'action_1' => [
                        'requireRows' => true,
                    ],
                    'action_2' => [
                        'requireRows' => false,
                    ],
                    'action_3' => [
                    ],
                ],
            ]
        ];

        $mockContentHelper = m::mock(\Common\Service\Table\ContentHelper::class);
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->with('{{[elements/actionButton]}}', m::any())
            ->andReturnUsing(
                function ($content, $details) {
                    return $details['name'];
                }
            );
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->once()
            ->with(
                '{{[elements/actionContainer]}}',
                [
                    'content' => 'action_2action_3',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'isInternalReadOnly']);
        $table
            ->setType(TableBuilder::TYPE_CRUD)
            ->setRows([])
            ->setSettings($settings);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->willReturn($mockContentHelper);

        $table->expects($this->any())
            ->method('isInternalReadOnly')
            ->willReturn(false);

        $table->renderActions();
    }

    /**
     * Test renderActions
     */
    public function testRenderActions()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'add' => [],
                    'edit' => [],
                    'foo' => [],
                    'bar' => [],
                    'cake' => [],
                    'baz' => [],
                ]
            ]
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->with('{{[elements/actionContainer]}}')
            ->will(
                $this->returnCallback(
                    function ($content, $vars) {
                        unset($content);
                        return $vars;
                    }
                )
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'renderButtonActions']);

        $table->setType(TableBuilder::TYPE_CRUD);
        $table->setSettings($settings);

        $table->expects($this->once())
            ->method('renderButtonActions')
            ->willReturn('EXPECTED');

        $table->expects($this->once())
            ->method('getContentHelper')
            ->willReturn($mockContentHelper);

        $this->assertEquals(['content' => 'EXPECTED'], $table->renderActions());
    }

    /**
     * Test renderActions With Dropdown
     */
    public function testRenderActionsWithDropdown()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'add' => [],
                    'edit' => [],
                    'foo' => [],
                    'bar' => [],
                    'cake' => [],
                    'baz' => [],
                    'top' => []
                ]
            ]
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->with('{{[elements/actionContainer]}}')
            ->will(
                $this->returnCallback(
                    function ($content, $vars) {
                        unset($content);
                        return $vars;
                    }
                )
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'renderDropdownActions']);

        $table->expects($this->once())
            ->method('renderDropdownActions')
            ->will($this->returnValue('DROPDOWN'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals(['content' => 'DROPDOWN'], $table->renderActions());
    }

    /**
     * Test renderActions with format override
     * (Default behaviour is dropdown for > 4 actions)
     */
    public function testRenderActionsWithFormatOverrideButtons()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'add' => [],
                    'edit' => [],
                    'foo' => [],
                    'bar' => [],
                    'cake' => []
                ]
            ],
            'actionFormat' => 'buttons'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->with('{{[elements/actionContainer]}}')
            ->will(
                $this->returnCallback(
                    function ($content, $vars) {
                        unset($content);
                        return $vars;
                    }
                )
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'renderButtonActions']);

        $table->expects($this->once())
            ->method('renderButtonActions')
            ->will($this->returnValue('BUTTONS'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals(['content' => 'BUTTONS'], $table->renderActions());
    }

    /**
     * Test renderActions with format override
     * (Default behaviour is buttons for <= 4 actions)
     */
    public function testRenderActionsWithFormatOverrideDropdown()
    {
        $settings = [
            'crud' => [
                'actions' => [
                    'foo' => [],
                    'bar' => [],
                ]
            ],
            'actionFormat' => 'dropdown'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->with('{{[elements/actionContainer]}}')
            ->will(
                $this->returnCallback(
                    function ($content, $vars) {
                        unset($content);
                        return $vars;
                    }
                )
            );

        $table = $this->getMockTableBuilder(['getContentHelper', 'renderDropdownActions']);

        $table->expects($this->once())
            ->method('renderDropdownActions')
            ->will($this->returnValue('DROPDOWN'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals(['content' => 'DROPDOWN'], $table->renderActions());
    }

    /**
     * Test renderAttributes
     */
    public function testRenderAttributes()
    {
        $attributes = [];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['renderAttributes']);

        $mockContentHelper->expects($this->once())
            ->method('renderAttributes')
            ->with($attributes);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderAttributes($attributes);
    }

    /**
     * Test renderAttributes without attributes
     */
    public function testRenderAttributesWithoutAttributes()
    {
        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['renderAttributes']);

        $mockContentHelper->expects($this->once())
            ->method('renderAttributes')
            ->with([]);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderAttributes();
    }

    /**
     * Test renderDropdownActions
     */
    public function testRenderDropdownActions()
    {
        $actions = [
            [
                'foo1' => 'bar1'
            ],
            [
                'foo2' => 'bar2'
            ]
        ];

        $mockContentHelper = m::mock(ContentHelper::class)->makePartial();

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/actionOption]}}', ['foo1' => 'bar1'])
            ->andReturn('option1');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/actionOption]}}', ['foo2' => 'bar2'])
            ->andReturn('option2');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/actionSelect]}}', ['option' => 'option1option2', 'action_field_name' => 'action'])
            ->andReturn('content');

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('content', $table->renderDropdownActions($actions));
    }

    /**
     * Test renderButtonActions
     */
    public function testRenderButtonActions()
    {
        $actions = [
            [
                'foo1' => 'bar1'
            ],
            [
                'foo2' => 'bar2'
            ]
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/actionButton]}}', ['foo1' => 'bar1']);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/actionButton]}}', ['foo2' => 'bar2']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderButtonActions($actions);
    }

    /**
     * Test renderButtonActions with collapseAt value set
     */
    public function testRenderButtonActionsCollapse()
    {
        $actions = [
            [
                'foo' => 'bar'
            ],
            [
                'bar' => 'cake'
            ],
            [
                'action_3' => 'unit_1|',
            ],
            [
                'action_4' => 'unit_2|',
            ],
        ];

        $mockContentHelper = m::mock(\Common\Service\Table\ContentHelper::class);
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->times(4)
            ->with('{{[elements/actionButton]}}', m::any())
            ->andReturnUsing(
                function ($content, $details) {
                    return key($details) . '-' . current($details);
                }
            );
        $mockContentHelper
            ->shouldReceive('replaceContent')
            ->with(
                '{{[elements/moreActions]}}',
                [
                    'content' => 'action_3-unit_1|action_4-unit_2|',
                    'label' => self::TRANSLATED . 'table_button_more_actions',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->willReturn($mockContentHelper);

        $table->renderButtonActions($actions, 2);
    }

    /**
     * Test renderFooter Without pagination
     */
    public function testRenderFooterWithoutPagination()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setType(TableBuilder::TYPE_CRUD);

        $this->assertEquals('', $table->renderFooter());
    }

    /**
     * Test renderFooter
     */
    public function testRenderFooter()
    {
        $settings = [
            'paginate' => [
                'limit' => [
                    'options' => [10, 20, 30]
                ]
            ]
        ];

        $table = $this->getMockTableBuilder(['renderLayout']);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('pagination');

        $table->setSettings($settings);

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $table->setLimit(10);

        $table->setTotal(100);

        $table->renderFooter();
    }

    /**
     * Test renderLimitOptions Without limit options
     *
     * @depends testRenderLimitOptions_IsDefined
     */
    public function testRenderLimitOptions_WithoutLimitOptions()
    {
        $settings = [
            'paginate' => [
                'limit' => [
                    'options' => []
                ]
            ]
        ];

        $table = $this->getConcreteTableBuilder();

        $table->setSettings($settings);

        $this->assertEquals('', $table->renderLimitOptions());
    }

    /**
     * Test renderLimitOptions
     *
     * @depends testRenderLimitOptions_IsDefined
     */
    public function testRenderLimitOptions()
    {
        $settings = [
            'paginate' => [
                'limit' => [
                    'options' => [
                        10, 20, 30
                    ]
                ]
            ]
        ];

        $mockContentHelper = m::mock(ContentHelper::class)->makePartial();

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '10', 'link' => ''])
            ->andReturn('10');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => PaginationHelper::CLASS_PAGINATION_ITEM_CURRENT, 'option' => '10']);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '20', 'link' => ''])
            ->andReturn('20');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => '', 'option' => '20']);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '30', 'link' => ''])
            ->andReturn('30');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => '', 'option' => '30']);

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $table = $this->getMockTableBuilder(['getContentHelper', 'getUrl']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->setSettings($settings);

        $table->setLimit(10);

        $this->assertEquals('', $table->renderLimitOptions());
    }

    /**
     * Test renderLimitOptions with query enabled
     *
     * @depends testRenderLimitOptions_IsDefined
     */
    public function testRenderLimitOptions_WithQueryEnabled()
    {
        $settings = [
            'paginate' => [
                'limit' => [
                    'options' => [
                        10, 20, 30
                    ]
                ]
            ]
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '10', 'link' => '?foo=bar&page=1&limit=30'])
            ->andReturn('10');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => PaginationHelper::CLASS_PAGINATION_ITEM_CURRENT, 'option' => '10']);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '20', 'link' => '?foo=bar&page=1&limit=30'])
            ->andReturn('20');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => '', 'option' => '20']);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitLink]}}', ['option' => '30', 'link' => '?foo=bar&page=1&limit=30'])
            ->andReturn('30');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/limitOption]}}', ['class' => '', 'option' => '30']);

        $mockQuery = [
            'foo' => 'bar',
            'page' => '1',
            'limit' => '30'
        ];

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);
        $mockUrl->expects($this->any())
            ->method('fromRoute')
            ->will($this->returnValue('?' . http_build_query($mockQuery)));

        $table = $this->getMockTableBuilder(['getContentHelper', 'getQuery', 'getUrl']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        $table->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->setSettings($settings);

        $table->setLimit(10);

        $this->assertEquals('', $table->renderLimitOptions());
    }

    /**
     * @depends renderPageOptions_IsDefined
     */
    public function testRenderPageOptions_WithoutOptions()
    {
        $options = [
            'previous' => [],
            'next' => [],
            'links' => [],
        ];

        $mockPaginationHelper = $this->createPartialMock(PaginationHelper::class, ['getOptions']);

        $mockPaginationHelper->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $table = $this->getMockTableBuilder(['getPaginationHelper']);

        $table->expects($this->once())
            ->method('getPaginationHelper')
            ->will($this->returnValue($mockPaginationHelper));

        $this->assertEquals('', $table->renderPageOptions());
    }

    /**
     * @depends renderPageOptions_IsDefined
     */
    public function testRenderPageOptions()
    {
        $options = [
            'previous' => [
                'page' => 1,
                'limit' => 10,
                'link' => '',
            ],
            'links' => [
                2 => [
                    'page' => 2,
                    'label' => '2',
                    'link' => '',
                ],
                'ellipse' => [
                    'page' => null,
                    'label' => '...',
                ],
                3 => [
                    'page' => 3,
                    'label' => '3',
                    'link' => '',
                ],
            ],
            'next' => [
                'page' => 4,
                'limit' => 10,
                'link' => '',
            ],
        ];

        $mockPaginationHelper = $this->createPartialMock(PaginationHelper::class, ['getOptions']);

        $mockPaginationHelper->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockContentHelper = m::mock(ContentHelper::class);

        $paginationLinkPrevious = '[paginationLinkPrevious]';
        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationPrevious]}}', $options['previous'])
            ->andReturn($paginationLinkPrevious);

        $paginationLinkNext = '[paginationLinkNext]';
        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationNext]}}', $options['next'])
            ->andReturn($paginationLinkNext);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationEllipses]}}', $options['links']['ellipse'])
            ->andReturn('[paginationLinkEllipse]');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationLink]}}', $options['links'][2])
            ->andReturn('[paginationLink1]');

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationLink]}}', $options['links'][3])
            ->andReturn('[paginationLink2]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/paginationItem]}}',
                [
                    'class' => '',
                    'page' => 2,
                    'label' => '2',
                    'link' => '',
                    'option' => '[paginationLink1]',
                ]
            )
            ->andReturn('[linkedPaginationItem1]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/paginationItem]}}',
                [
                    'class' => '',
                    'page' => 3,
                    'label' => '3',
                    'link' => '',
                    'option' => '[paginationLink2]',
                ]
            )
            ->andReturn('[linkedPaginationItem2]');

        $expectedListContents = '[linkedPaginationItem1][paginationLinkEllipse][linkedPaginationItem2]';
        $expectedListsMarkup = '<ul>' . $expectedListContents . '</ul>';

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/paginationList]}}', ['items' => $expectedListContents])
            ->andReturn($expectedListsMarkup);

        $table = $this->getMockTableBuilder(['getPaginationHelper', 'getUrl', 'getContentHelper']);

        $table->setPage(2);

        $table->expects($this->once())
            ->method('getPaginationHelper')
            ->will($this->returnValue($mockPaginationHelper));

        $table->expects($this->exactly(4))
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals(
            $paginationLinkPrevious . $expectedListsMarkup . $paginationLinkNext,
            $table->renderPageOptions()
        );
    }

    /**
     * Test renderHeaderColumn Without options
     */
    public function testRenderHeaderColumn_WithoutOptions()
    {
        $column = [];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/th]}}');

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column);
    }

    /**
     * Test renderHeaderColumn With custom content
     */
    public function testRenderHeaderColumn_WithCustomContent()
    {
        $column = [];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/foo]}}');

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column, '{{[elements/foo]}}');
    }

    /**
     * Test renderHeaderColumn With sort current order asc
     */
    public function testRenderHeaderColumn_WithSortCurrentOrderAsc()
    {
        $column = [
            'sort' => 'foo'
        ];

        $expectedColumn = [
            'sort' => 'foo',
            'scope' => 'col',
            'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable ascending',
            'aria' => '_TRSLTD_sort-in-descending-order',
            'order' => 'DESC',
            'link' => 'LINK'
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn)
            ->andReturn('[generatedSortColumn]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/foo]}}',
                [
                    'sort' => 'foo',
                    'scope' => 'col',
                    'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable ascending',
                    'order' => 'DESC',
                    'aria' => '_TRSLTD_sort-in-descending-order',
                    'link' => 'LINK',
                    'title' => '[generatedSortColumn]'
                ]
            )
            ->andReturn('[generatedFoo]');

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(['getContentHelper', 'getUrl']);

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('foo');
        $table->setOrder('ASC');

        $this->assertEquals(
            '[generatedFoo]',
            $table->renderHeaderColumn($column, '{{[elements/foo]}}')
        );
    }

    /**
     * Test renderHeaderColumn With sort current order desc
     */
    public function testRenderHeaderColumn_WithSortCurrentOrderDesc()
    {
        $column = [
            'sort' => 'foo'
        ];

        $expectedColumn = [
            'sort' => 'foo',
            'scope' => 'col',
            'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable descending',
            'order' => 'ASC',
            'aria' => '_TRSLTD_sort-in-ascending-order',
            'link' => 'LINK'
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn)
            ->andReturn('[generatedSortColumn]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/foo]}}',
                [
                    'sort' => 'foo',
                    'scope' => 'col',
                    'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable descending',
                    'order' => 'ASC',
                    'aria' => '_TRSLTD_sort-in-ascending-order',
                    'link' => 'LINK',
                    'title' => '[generatedSortColumn]',
                ]
            )
            ->andReturn('[generatedFoo]');

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(['getContentHelper', 'getUrl']);

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('foo');
        $table->setOrder('DESC');

        $this->assertEquals(
            '[generatedFoo]',
            $table->renderHeaderColumn($column, '{{[elements/foo]}}')
        );
    }

    /**
     * Test renderHeaderColumn With sort
     */
    public function testRenderHeaderColumn_WithSort()
    {
        $column = [
            'sort' => 'foo'
        ];

        $expectedColumn = [
            'sort' => 'foo',
            'scope' => 'col',
            'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable',
            'order' => 'ASC',
            'aria' => '_TRSLTD_sort-in-ascending-order',
            'link' => 'LINK'
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn)
            ->andReturn('[generatedSortColumn]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/foo]}}',
                [
                    'sort' => 'foo',
                    'scope' => 'col',
                    'class' => TableBuilder::CLASS_TABLE_HEADER . ' sortable',
                    'order' => 'ASC',
                    'aria' => '_TRSLTD_sort-in-ascending-order',
                    'link' => 'LINK',
                    'title' => '[generatedSortColumn]',
                ]
            )
            ->andReturn('[generatedFoo]');

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(['getContentHelper', 'getUrl']);

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('bar');
        $table->setOrder('DESC');

        $this->assertEquals(
            '[generatedFoo]',
            $table->renderHeaderColumn($column, '{{[elements/foo]}}')
        );
    }

    /**
     * Test renderHeaderColumn With pre-set width
     */
    public function testRenderHeaderColumn_WithWidthAndTitle()
    {
        $column = [
            'width' => 'checkbox',
            'title' => 'Title',
        ];

        $expectedColumn = [
            'width' => '20px',
            'title' => self::TRANSLATED . 'Title',
            'scope' => 'col',
            'class' => TableBuilder::CLASS_TABLE_HEADER,
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/th]}}', $expectedColumn);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column);
    }

    /**
     * Test renderHeaderColumn when disabled
     */
    public function testRenderHeaderColumn_WhenDisabled()
    {
        $column = [
            'hideWhenDisabled' => true
        ];

        $table = $this->getMockTableBuilder(['getContentHelper']);
        $table->setDisabled(true);

        $response = $table->renderHeaderColumn($column);

        $this->assertEquals(null, $response);
    }


    /**
     * Test renderHeaderColumn with alignment
     */
    public function testRenderHeaderColumn_WithAlign()
    {
        $column = [
            'align' => 'right',
        ];

        $expectedColumn = [
            'class' => TableBuilder::CLASS_TABLE_HEADER . ' right',
            'scope' => 'col'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/th]}}', $expectedColumn);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column);
    }


    /**
     * Test renderHeaderColumn with sort and alignment
     */
    public function testRenderHeaderColumn_WithSortAndAlign()
    {
        $column = [
            'sort' => 'foo',
            'align' => 'right',
        ];

        $expectedColumn = [
            'class' => TableBuilder::CLASS_TABLE_HEADER . ' right sortable',
            'sort' => 'foo',
            'scope' => 'col',
            'order' => 'ASC',
            'aria' => '_TRSLTD_sort-in-ascending-order',
            'link' => 'LINK',
        ];

        $mockContentHelper = m::mock(ContentHelper::class);

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn)
            ->andReturn('[generatedSortColumn]');

        $mockContentHelper->expects('replaceContent')
            ->with(
                '{{[elements/th]}}',
                [
                    'sort' => 'foo',
                    'scope' => 'col',
                    'class' => TableBuilder::CLASS_TABLE_HEADER . ' right sortable',
                    'order' => 'ASC',
                    'aria' => '_TRSLTD_sort-in-ascending-order',
                    'link' => 'LINK',
                    'title' => '[generatedSortColumn]',
                ]
            )
            ->andReturn('[generatedTh]');

        $mockUrl = $this->createPartialMock(Url::class, ['fromRoute']);

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(['getContentHelper', 'getUrl']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $this->assertEquals(
            '[generatedTh]',
            $table->renderHeaderColumn($column)
        );
    }

    /**
     * Test renderBodyColumn when disabled
     */
    public function testRenderBodyColumnWhenDisabled()
    {
        $column = [
            'hideWhenDisabled' => true
        ];

        $table = $this->getMockTableBuilder(['getContentHelper']);
        $table->setDisabled(true);

        $response = $table->renderBodyColumn([], $column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderHeaderColumn when incorrect permission set
     */
    public function testRenderHeaderColumn_WhenPermissionWontAllow()
    {
        $column = [
            'permissionRequisites' => ['incorrectPermission']
        ];

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $response = $table->renderHeaderColumn($column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderBodyColumn when incorrect permission set
     */
    public function testRenderBodyColumnWhenPermissionWontAllow()
    {
        $column = [
            'permissionRequisites' => ['incorrectPermission']
        ];

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $response = $table->renderBodyColumn([], $column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderHeaderColumn when correct permission set
     */
    public function testRenderHeaderColumn_WhenPermissionWillAllow()
    {
        $column = [
            'permissionRequisites' => ['correctPermission']
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);
        $mockContentHelper->expects($this->once())
            ->method('replaceContent');

        $mockAuthService = $this->createPartialMock(AuthorizationService::class, ['isGranted']);
        $mockAuthService->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $constructorArgs = [
            $this->getMockServiceLocator(),
            $mockAuthService,
            $this->getMockTranslator(),
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        ];

        $table = $this->getMockTableBuilder(['getContentHelper'], $constructorArgs);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $response = $table->renderHeaderColumn($column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderBodyColumn when correct permission set
     */
    public function testRenderBodyColumnWhenPermissionWillAllow()
    {
        $column = [
            'permissionRequisites' => ['correctPermission']
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);
        $mockContentHelper->expects($this->once())
            ->method('replaceContent');

        $mockAuthService = $this->createPartialMock(AuthorizationService::class, ['isGranted']);
        $mockAuthService->expects($this->once())
            ->method('isGranted')
            ->willReturn(true);

        $constructorArgs = [
            $this->getMockServiceLocator(),
            $mockAuthService,
            $this->getMockTranslator(),
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        ];

        $table = $this->getMockTableBuilder(['getContentHelper'], $constructorArgs);
        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $response = $table->renderBodyColumn([], $column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderBodyColumn With Empty Row With Empty Column
     */
    public function testRenderBodyColumnEmptyRowEmptyColumn()
    {
        $row = [];

        $column = [];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => '', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Name
     */
    public function testRenderBodyColumnWithName()
    {
        $row = [
            'foo' => 'bar'
        ];

        $column = [
            'name' => 'foo'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => 'bar', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Align
     */
    public function testRenderBodyColumnWithAlign()
    {
        $row = [
            'foo' => 'bar'
        ];

        $column = [
            'name' => 'foo',
            'align' => 'right',
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => 'bar', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . ' right"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn with data heading
     */
    public function testRenderBodyColumnWithDataHeading()
    {
        $row = [
            'foo' => 'bar'
        ];

        $column = [
            'name' => 'foo',
            'title' => '<div>Foo</div>',
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => 'bar', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '" data-heading="_TRSLTD_Foo"']);

        $table = $this->getMockTableBuilder(['getContentHelper', 'getColumns']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue([$column]));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn with data custom attributes
     */
    public function testRenderBodyColumnWithAttributes()
    {
        $row = [
            'foo' => 'bar'
        ];

        $column = [
            'name' => 'foo',
            'align' => 'centre'
        ];

        $customAttributes = ['colspan' => '2', 'class' => 'a-class', 'data-empty' => ' '];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => 'bar', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . ' centre a-class" colspan="2"']);

        $table = $this->getMockTableBuilder(['getContentHelper', 'getColumns']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue([$column]));

        $table->renderBodyColumn($row, $column, '{{[elements/td]}}', $customAttributes);
    }

    /**
     * Test renderBodyColumn Custom Wrapper
     */
    public function testRenderBodyColumnCustomWrapper()
    {
        $row = [];

        $column = [];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/foo]}}', ['content' => '', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column, '{{[elements/foo]}}');
    }

    /**
     * Test renderBodyColumn With Format
     */
    public function testRenderBodyColumnWithFormat()
    {
        $row = [
            'test' => 'bar'
        ];

        $column = [
            'format' => 'FOO'
        ];

        $mockContentHelper = m::mock(ContentHelper::class)->makePartial();

        $mockContentHelper->expects('replaceContent')
            ->with('FOO', $row)
            ->andReturn('FOOBAR');

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Formatter
     */
    public function testRenderBodyColumnWithFormatter()
    {
        $row = [
            'date' => date('Y-m-d')
        ];

        $column = [
            'formatter' => Date::class,
            'name' => 'date'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => date('d/m/Y'), 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->mockFormatterPluginManager->shouldReceive('has')->with(Date::class)->andReturn(true);
        $mockDateFormatter = m::mock(Date::class)->makePartial();
        $mockDateFormatter->shouldReceive('format')->with(date('Y-m-d'))->andReturn(date('d/m/Y'));
        $this->mockFormatterPluginManager->shouldReceive('get')->with(Date::class)->andReturn($mockDateFormatter);

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Formatter And Action Type
     */
    public function testRenderBodyColumnWithFormatterAndActionType()
    {
        $row = [
            'id' => 1,
            'date' => date('Y-m-d')
        ];

        $column = [
            'type' => 'Action',
            'class' => '',
            'action' => 'edit',
            'formatter' => Date::class,
            'name' => 'date'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $expected = '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="action[edit][1]" >' . date('d/m/Y') . '</button>';
        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                ['content' => $expected, 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $mockFormatterPluginManager = m::mock(FormatterPluginManager::class);
        $mockFormatterPluginManager->shouldReceive('has')->andReturn(true);

        $this->mockFormatterPluginManager->shouldReceive('has')->with(Date::class)->andReturn(true);
        $mockDateFormatter = m::mock(Date::class)->makePartial();
        $mockDateFormatter->shouldReceive('format')->with(date('Y-m-d'))->andReturn(date('d/m/Y'));
        $this->mockFormatterPluginManager->shouldReceive('get')->with(Date::class)->andReturn($mockDateFormatter);

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Invalid Formatter
     */
    public function testRenderBodyColumnWithInvalidFormatter()
    {
        $this->expectException(MissingFormatterException::class);
        $this->expectExceptionMessage('Missing table formatter: Blah');

        $column = [
            'formatter' => 'Blah'
        ];

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->renderBodyColumn([], $column);
    }

    /**
     * Test renderBodyColumn With Formatter Returning Array
     */
    public function testRenderBodyColumnWithFormatterReturningArray()
    {
        $row = [
            'date' => date('Y-m-d')
        ];

        $column = [
            'formatter' => function () {
                return ['date' => 'Something Else'];
            },
            'name' => 'date'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', ['content' => 'Something Else', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']);

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));


        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Selector Type
     */
    public function testRenderBodyColumnWithSelectorType()
    {
        $row = [
            'id' => 1
        ];

        $column = [
            'type' => 'Selector'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                ['content' => '<input type="radio" name="id" value="1" id="[id][1]" />', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Selector Type And Fieldset
     */
    public function testRenderBodyColumnWithSelectorTypeAndFieldset()
    {
        $row = [
            'id' => 1
        ];

        $column = [
            'type' => 'Selector'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                ['content' => '<input type="radio" name="table[id]" value="1" id="table[id][1]" />', 'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"']
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->setFieldset('table');

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Action Type
     */
    public function testRenderBodyColumnWithActionType()
    {
        $row = [
            'id' => 1,
            'foo' => 'bar'
        ];

        $column = [
            'type' => 'Action',
            'name' => 'foo',
            'class' => '',
            'action' => 'edit'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                [
                    'content' => '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="action[edit][1]" >bar</button>',
                    'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Action Type And Fieldset
     */
    public function testRenderBodyColumnWithActionTypeAndFieldset()
    {
        $row = [
            'id' => 1,
            'foo' => 'bar'
        ];

        $column = [
            'type' => 'Action',
            'name' => 'foo',
            'class' => '',
            'action' => 'edit'
        ];

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                [
                    'content' => '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][edit][1]" >bar</button>',
                    'attrs' => ' class="' . TableBuilder::CLASS_TABLE_CELL . '"',
                ]
            );

        $table = $this->getMockTableBuilder(['getContentHelper']);

        $table->setFieldset('table');

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderExtraRows with rows
     */
    public function testRenderExtraRowsWithRows()
    {
        $table = $this->getMockTableBuilder(['getRows']);

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue(['foo' => 'bar']));

        $this->assertEquals('', $table->renderExtraRows());
    }

    /**
     * Test renderExtraRows without rows with custom message
     */
    public function testRenderExtraRowsWithoutRowsCustomMessage()
    {
        $mockTranslator = $this->createPartialMock(Translator::class, ['translate']);

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return $string;
                    }
                )
            );

        $constructorArgs = [
            $this->getMockServiceLocator(),
            $this->getMockAuthService(),
            $mockTranslator,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        ];

        $table = $this->getMockTableBuilder(
            ['getRows', 'getColumns', 'getContentHelper', 'getServiceLocator'],
            $constructorArgs
        );

        $table->setVariables(['empty_message' => 'Empty']);

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue([]));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(['foo']));

        $mockContentHelper = m::mock(ContentHelper::class)->makePartial();

        $mockContentHelper->expects('replaceContent')
            ->with('{{[elements/emptyRow]}}', ['colspan' => 1, 'message' => 'Empty'])
            ->andReturn('CONTENT');

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('CONTENT', $table->renderExtraRows());
    }

    /**
     * Test renderExtraRows without rows
     */
    public function testRenderExtraRowsWithoutRows()
    {
        $mockTranslator = $this->createPartialMock(Translator::class, ['translate']);

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return $string;
                    }
                )
            );

        $constructorArgs = [
            $this->getMockServiceLocator(),
            $this->getMockAuthService(),
            $mockTranslator,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        ];

        $table = $this->getMockTableBuilder(
            ['getRows', 'getColumns', 'getContentHelper', 'getServiceLocator'],
            $constructorArgs
        );

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue([]));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(['foo']));

        $mockContentHelper = $this->createPartialMock(ContentHelper::class, ['replaceContent']);

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/emptyRow]}}', ['colspan' => 1, 'message' => 'The table is empty'])
            ->will($this->returnValue('CONTENT'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('CONTENT', $table->renderExtraRows());
    }

    /**
     * Test action field name and fieldset
     */
    public function testActionFieldNameAndFieldset()
    {
        $actionName = 'foo';

        $fieldset = 'table';

        $table = $this->getConcreteTableBuilder();

        $table->setActionFieldName($actionName);

        $this->assertEquals($actionName, $table->getActionFieldName());

        $table->setFieldset($fieldset);

        $this->assertEquals($fieldset, $table->getFieldset());

        $this->assertEquals($fieldset . '[' . $actionName . ']', $table->getActionFieldName());
    }

    /**
     * Test get and set footer
     */
    public function testGetFooter()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setFooter(['Foo' => 'Bar']);

        $this->assertEquals(['Foo' => 'Bar'], $table->getFooter());
    }

    /**
     * Test get and set variable
     */
    public function testGetVariable()
    {
        $table = $this->getConcreteTableBuilder();

        $vars = [
            'foo' => 'bar',
            'bar' => 'cake'
        ];

        $table->setVariables($vars);

        $this->assertEquals('bar', $table->getVariable('foo'));

        $table->setVariable('foo', 'cake');

        $this->assertEquals('cake', $table->getVariable('foo'));
    }

    /**
     * Test remove column method
     */
    public function testRemoveColumn()
    {
        $table = $this->getConcreteTableBuilder();

        $columns = [
            ['name' => 'name1'],
            ['name' => 'name2']
        ];

        $table->setColumns($columns);

        $this->assertTrue($table->hasColumn('name1'));

        $table->removeColumn('name1');

        $this->assertFalse($table->hasColumn('name1'));
    }

    /**
     * Test remove column method if no name property exists
     */
    public function testRemoveColumnNoNameExists()
    {
        $table = $this->getConcreteTableBuilder();
        $columns = [
            ['name' => 'name1'],
            ['foo' => 'bar']
        ];
        $table->setColumns($columns);
        $table->removeColumn('name1');
        $newColumns = $table->getColumns();
        $this->assertEquals(count($newColumns), 1);
    }

    /**
     * Test get and set settings
     */
    public function testGetSettings()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setSettings(['Foo' => 'Bar']);

        $this->assertEquals(['Foo' => 'Bar'], $table->getSettings());
    }

    public function testIsRowDisabled()
    {
        // Stubbed data
        $settings = [];
        $row = [];

        $mockAuthService = m::mock(AuthorizationService::class);
        $mockAuthService->shouldReceive('isGranted')
            ->with(m::type('string'))
            ->andReturn(true);
        $mockTranslatorService = m::mock(\Laminas\Mvc\I18n\Translator::class);

        // Setup
        $sut = new TableBuilder(
            $this->getMockServiceLocator(),
            $mockAuthService,
            $mockTranslatorService,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $sut->setSettings($settings);

        $this->assertFalse($sut->isRowDisabled($row));
    }

    /**
     * @dataProvider providerIsRowDisabled
     */
    public function testIsRowDisabledWithDisabled($disabled)
    {
        // Stubbed data
        $settings = [
            'row-disabled-callback' => function ($row) {
                return $row['disabled'];
            }
        ];
        $row = [
            'disabled' => $disabled
        ];

        $mockAuthService = m::mock(AuthorizationService::class);
        $mockAuthService->shouldReceive('isGranted')
            ->with(m::type('string'))
            ->andReturn(true);
        $mockTranslatorService = m::mock(\Laminas\Mvc\I18n\Translator::class);

        // Setup
        $sut = new TableBuilder(
            $this->getMockServiceLocator(),
            $mockAuthService,
            $mockTranslatorService,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $sut->setSettings($settings);

        $this->assertEquals($disabled, $sut->isRowDisabled($row));
    }

    public function providerIsRowDisabled()
    {
        return [
            [true],
            [false]
        ];
    }

    public function testRemoveActions()
    {
        $tableConfig = [
            'crud' => [
                'actions' => [
                    'foo' => [],
                    'bar' => [],
                ]
            ]
        ];

        /** @var TableBuilder $table */
        $table = m::mock(TableBuilder::class)->makePartial();

        $table->setSettings($tableConfig);

        $table->expects('removeAction')
            ->with('foo');

        $table->expects('removeAction')
            ->with('bar');

        $table->expects('removeColumn')
            ->with('actionLinks');

        $table->removeActions();
    }

    public function testDisableAction()
    {
        $tableConfig = [
            'settings' => [
                'paginate' => [],
                'crud' => [
                    'actions' => [
                        'foo' => [],
                        'bar' => []
                    ]
                ]
            ]
        ];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $table->loadConfig('test');

        $table->disableAction('foo');

        $this->assertEquals(
            [
                'foo' => ['disabled' => 'disabled'],
                'bar' => [],
            ],
            $table->getSettings()['crud']['actions']
        );
    }

    public function testGetEmptyMessage()
    {
        $message = 'foo';
        $config = [
            'tables' => [
                'config' => [__DIR__ . '/TestResources/'],
                'partials' => [
                    'html' => '',
                    'csv' => ''
                ]
            ]
        ];
        $mockAuthService = m::mock(AuthorizationService::class);
        $mockAuthService->shouldReceive('isGranted')
            ->with(m::type('string'))
            ->andReturn(true);

        $mockTranslator = m::mock(Translator::class);
        $mockTranslator->shouldReceive('translate')
            ->with($message)
            ->andReturn($message);

        $sut = new TableBuilder(
            $this->getMockServiceLocator(),
            $mockAuthService,
            $mockTranslator,
            $this->getMockUrlHelperService(),
            $config,
            $this->mockFormatterPluginManager
        );

        $sut->setEmptyMessage($message);
        $this->assertEquals($message, $sut->getEmptyMessage());
    }

    public function testAddAction()
    {
        $tableConfig = [
            'settings' => [
                'paginate' => [],
                'crud' => [
                    'actions' => [
                        'foo' => [],
                        'bar' => []
                    ]
                ]
            ]
        ];

        $table = $this->getMockTableBuilder(['getConfigFromFile']);

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $table->loadConfig('test');

        $table->addAction('new', ['key' => 'value']);

        $settings = $table->getSetting('crud');

        $this->assertEquals(
            [
                'actions' => [
                    'foo' => [],
                    'bar' => [],
                    'new' => ['key' => 'value']
                ]
            ],
            $settings
        );
    }

    public function testCheckForActionLinks()
    {
        $tableConfig = [
            'settings' => [
                'paginate' => [],
                'crud' => [
                    'actions' => []
                ]
            ],
            'columns' => [
                ['bar'],
                [
                    'type' => 'ActionLinks',
                    'keepForReadOnly' => true,
                ],
                [
                    'type' => 'ActionLinks',
                ],
                [
                    'type' => 'DeltaActionLinks'
                ]
            ]
        ];

        $mockTranslator = m::mock(Translator::class);
        $mockTranslator->shouldReceive('translate')
            ->with(m::type('string'))
            ->andReturn('foo');

        $mockAuthService = m::mock(AuthorizationService::class);
        $mockAuthService->shouldReceive('isGranted')
            ->with('internal-user')
            ->andReturn(true)
            ->once()
            ->shouldReceive('isGranted')
            ->with('internal-edit')
            ->andReturn(false)
            ->once()
            ->getMock();

        $sut = new TableBuilder(
            $this->getMockServiceLocator(),
            $mockAuthService,
            $mockTranslator,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $sut->loadConfig($tableConfig);
        $this->assertEquals(
            [
                ['bar'],
                [
                    'type' => 'ActionLinks',
                    'keepForReadOnly' => true,
                ],
            ],
            $sut->getColumns()
        );
    }

    public function testSetSetting()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setSetting('collapseAt', 2);
        $this->assertEquals(2, $table->getSetting('collapseAt'));
    }

    public function testGetAction()
    {
        $table = $this->getConcreteTableBuilder();

        $table->setSetting('crud', ['actions' => []]);
        $action = ['foo', 'bar'];
        $table->addAction('add', $action);
        $this->assertEquals($table->getAction('add'), $action);
    }

    /**
     * @test
     */
    public function getUrlParameterNameMap_IsDefined()
    {
        // Set Up
        $table = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$table, 'getUrlParameterNameMap']);
    }

    /**
     * @depends getUrlParameterNameMap_IsDefined
     * @test
     */
    public function getUrlParameterNameMap_ReturnsAnArray()
    {
        // Set Up
        $table = $this->setUpSut();

        // Execute
        $map = $table->getUrlParameterNameMap();

        // Assert
        $this->assertIsArray($map);

        return $map;
    }

    /**
     * @depends getUrlParameterNameMap_ReturnsAnArray
     * @test
     */
    public function getUrlParameterNameMap_ReturnsAnEmptyArrayByDefault($map)
    {
        // Assert
        $this->assertEmpty($map);
    }

    /**
     * @test
     */
    public function setUrlParameterNameMap_IsDefined()
    {
        // Set Up
        $table = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$table, 'setUrlParameterNameMap']);
    }

    /**
     * @depends setUrlParameterNameMap_IsDefined
     * @test
     */
    public function setUrlParameterNameMap_ReturnsSelf()
    {
        // Set Up
        $table = $this->setUpSut();

        // Execute
        $result = $table->setUrlParameterNameMap([]);

        // Assert
        $this->assertSame($result, $table);
    }

    /**
     * @depends setUrlParameterNameMap_IsDefined
     * @depends getUrlParameterNameMap_IsDefined
     * @test
     */
    public function setUrlParameterNameMap_SetsMappings()
    {
        // Set Up
        $table = $this->setUpSut();
        $expectedMappings = ['foo' => 'bar'];

        // Execute
        $table->setUrlParameterNameMap($expectedMappings);

        // Assert
        $this->assertEquals($expectedMappings, $table->getUrlParameterNameMap());
    }

    /**
     * @test
     */
    public function testRenderLimitOptions_IsDefined()
    {
        // Set Up
        $table = $this->getConcreteTableBuilder();

        // Assert
        $this->assertIsCallable([$table, 'renderLimitOptions']);
    }

    /**
     * @return string[][]
     */
    public function pageAndLimitUrlParameterNamesDataProvider(): array
    {
        return [
            'default query parameter name name used for limiting query results' => ['limit'],
            'default query parameter name name used for selecting a page' => ['page'],
        ];
    }

    /**
     * @depends      testRenderLimitOptions_IsDefined
     * @dataProvider pageAndLimitUrlParameterNamesDataProvider
     * @test
     */
    public function renderLimitOptions_DefaultUrlParameterNames(string $urlParameterName)
    {
        // Set Up
        $table = $this->setUpSut();
        $table->setSetting('paginate', ['limit' => ['options' => ['1']]]);

        // Define Expectations
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($urlParameterName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();
        $table->getUrl()->shouldReceive('fromRoute')->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderLimitOptions();
    }

    /**
     * @return string[][]
     */
    public function mappedPageAndLimitUrlParameterNamesDataProvider(): array
    {
        return [
            'mapped limit url parameter' => ['limit', 'foo'],
            'mapped page url parameter' => ['page', 'bar'],
        ];
    }

    /**
     * @param string $originalName
     * @param string $mappedName
     * @depends      renderLimitOptions_DefaultUrlParameterNames
     * @dataProvider mappedPageAndLimitUrlParameterNamesDataProvider
     * @test
     */
    public function renderLimitOptions_MapsUrlParameterNames(string $originalName, string $mappedName)
    {
        // Set Up
        $table = $this->setUpSut();
        $table->setUrlParameterNameMap([$originalName => $mappedName]);
        $table->loadParams(['url' => $table->getUrl()]);
        $table->setSetting('paginate', ['limit' => ['options' => ['1']]]);
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($mappedName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();

        // Define Expectations
        $table->getUrl()->shouldReceive('fromRoute')->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderLimitOptions();
    }

    /**
     * @test
     */
    public function renderPageOptions_IsDefined()
    {
        // Set Up
        $table = $this->getConcreteTableBuilder();

        // Assert
        $this->assertIsCallable([$table, 'renderPageOptions']);
    }

    /**
     * @param string $urlParameterName
     * @depends      renderPageOptions_IsDefined
     * @dataProvider pageAndLimitUrlParameterNamesDataProvider
     * @test
     */
    public function renderPageOptions_DefaultUrlParameterNames(string $urlParameterName)
    {
        // Set Up
        $table = $this->setUpSut();
        $table->setTotal(100);
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($urlParameterName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();

        // Define Expectations
        $table->getUrl()->shouldReceive('fromRoute')->atLeast()->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderPageOptions();
    }

    /**
     * @param string $originalName
     * @param string $mappedName
     * @depends      renderPageOptions_DefaultUrlParameterNames
     * @dataProvider mappedPageAndLimitUrlParameterNamesDataProvider
     * @test
     */
    public function renderPageOptions_MapsUrlParameterNames(string $originalName, string $mappedName)
    {
        // Set Up
        $table = $this->setUpSut();
        $table->setUrlParameterNameMap([$originalName => $mappedName]);
        $table->setTotal(100);
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($mappedName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();

        // Define Expectations
        $table->getUrl()->shouldReceive('fromRoute')->atLeast()->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderPageOptions();
    }

    /**
     * @test
     */
    public function renderHeaderColumn_IsDefined()
    {
        // Set Up
        $table = $this->getConcreteTableBuilder();

        // Assert
        $this->assertIsCallable([$table, 'renderHeaderColumn']);
    }

    /**
     * @return string[][]
     */
    public function sortAndOrderUrlParameterNamesDataProvider(): array
    {
        return [
            'default query parameter name name used for sorting query results' => ['sort'],
            'default query parameter name name used for ordering query results' => ['order'],
        ];
    }

    /**
     * @param string $urlParameterName
     * @depends      renderHeaderColumn_IsDefined
     * @dataProvider sortAndOrderUrlParameterNamesDataProvider
     * @test
     */
    public function renderHeaderColumn_DefaultUrlParameterNames(string $urlParameterName)
    {
        // Set Up
        $table = $this->setUpSut();
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($urlParameterName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();

        // Define Expectations
        $table->getUrl()->shouldReceive('fromRoute')->atLeast()->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderHeaderColumn(['sort' => 'foo']);
    }

    /**
     * @return string[][]
     */
    public function mappedSortAndOrderUrlParameterNamesDataProvider(): array
    {
        return [
            'mapped sort url parameter' => ['sort', 'foo'],
            'mapped order url parameter' => ['order', 'bar'],
        ];
    }

    /**
     * @param string $originalName
     * @param string $mappedName
     * @depends      renderHeaderColumn_DefaultUrlParameterNames
     * @dataProvider mappedSortAndOrderUrlParameterNamesDataProvider
     * @test
     */
    public function renderHeaderColumn_MapsUrlParameterNames(string $originalName, string $mappedName)
    {
        // Set Up
        $table = $this->setUpSut();
        $table->setUrlParameterNameMap([$originalName => $mappedName]);
        $table->setTotal(100);
        $queryWithLimitMatcher = IsArrayContainingKey::hasKeyInArray($mappedName);
        $optionsMatcher = IsArrayContainingKeyValuePair::hasKeyValuePair('query', $queryWithLimitMatcher);
        $any = IsAnything::anything();

        // Define Expectations
        $table->getUrl()->shouldReceive('fromRoute')->atLeast()->once()->with($any, $any, $optionsMatcher, $any);

        // Execute
        $table->renderHeaderColumn(['sort' => 'foo']);
    }

    /**
     * @return TableBuilder
     */
    protected function setUpSut(): TableBuilder
    {
        $sut = $this->getConcreteTableBuilder();

        $urlPlugin = m::mock(Url::class);
        $urlPlugin->shouldIgnoreMissing('');
        $sut->loadParams(['url' => $urlPlugin]);
        return $sut;
    }

    public function testGetAuthService()
    {
        $authService = m::mock(AuthorizationService::class);

        $tableBuilder = new TableBuilder(
            $this->getMockServiceLocator(),
            $authService,
            $this->getMockTranslator(),
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $this->assertSame(
            $authService,
            $tableBuilder->getAuthService()
        );
    }

    public function testGetTranslator()
    {
        $translator = m::mock(Translator::class);

        $tableBuilder = new TableBuilder(
            $this->getMockServiceLocator(),
            $this->getMockAuthService(),
            $translator,
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $this->assertSame(
            $translator,
            $tableBuilder->getTranslator()
        );
    }

    public function testGetServiceLocator()
    {
        $serviceLocator = m::mock(ContainerInterface::class);

        $tableBuilder = new TableBuilder(
            $serviceLocator,
            $this->getMockAuthService(),
            $this->getMockTranslator(),
            $this->getMockUrlHelperService(),
            $this->getMockConfig(),
            $this->mockFormatterPluginManager
        );

        $this->assertSame(
            $serviceLocator,
            $tableBuilder->getServiceLocator()
        );
    }
}
