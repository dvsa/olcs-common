<?php

/**
 * Table Builder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;
use Common\Service\Table\TableFactory;

/**
 * Table Builder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Get Mock Table Builder
     */
    private function getMockTableBuilder($methods = array())
    {
        return $this->getMock('\Common\Service\Table\TableBuilder', $methods, array($this->getMockServiceLocator()));
    }

    private function getMockServiceLocator($config = true)
    {

        $mockTranslator = $this->getMock('\stdClass', array('translate'));
        $mockSm = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));
        $mockControllerPluginManager = $this->getMock('\Zend\Mvc\Controller\PluginManager', array('get'));

        $servicesMap = [
            ['Config', true, ($config
                ? array(
                    'tables' => array(
                        'config' => array(__DIR__ . '/TestResources/'),
                        'partials' => ''
                    ),
                )
                : array())
            ],
            ['translator', true, $mockTranslator],
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

        $table = new TableBuilder($this->getMockServiceLocator());

        $contentHelper = $table->getContentHelper();

        $this->assertTrue($contentHelper instanceof \Common\Service\Table\ContentHelper);

        $contentHelper2 = $table->getContentHelper();

        $this->assertTrue($contentHelper === $contentHelper2);
    }

    /**
     * Test getContentHelper without configured partials
     *
     * @expectedException \Exception
     */
    public function testGetContentHelperWithoutConfig()
    {
        $table = new TableBuilder($this->getMockServiceLocator(false));

        $table->getContentHelper();
    }

    /**
     * Test getPaginationHelper
     */
    public function testGetPaginationHelper()
    {
        $table = new TableBuilder($this->getMockServiceLocator());

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
        $table = new TableBuilder($this->getMockServiceLocator());

        $config = $table->getConfigFromFile('sample');

        $this->assertEquals(array('foo' => 'bar'), $config);
    }

    /**
     * Test getConfigFromFile with missing file
     *
     * @expectedException \Exception
     */
    public function testGetConfigFromFileWithMissingFile()
    {
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->getConfigFromFile('DoesntExist');
    }

    /**
     * Test build table calls all of the relevant methods
     */
    public function testBuildTable()
    {
        $table = $this->getMock(
            '\Common\Service\Table\TableBuilder', array(
            'loadConfig',
            'loadData',
            'loadParams',
            'setupAction',
            'render'
            ), array($this->getMockServiceLocator())
        );

        $table->expects($this->at(0))
            ->method('loadConfig');

        $table->expects($this->at(1))
            ->method('loadData');

        $table->expects($this->at(2))
            ->method('loadParams');

        $table->expects($this->at(3))
            ->method('setupAction');

        $table->expects($this->at(4))
            ->method('render')
            ->will($this->returnValue('SomeHTML'));

        $this->assertEquals('SomeHTML', $table->buildTable('test'));
    }

    /**
     * Test build table without render
     */
    public function testBuildTableWithoutRender()
    {
        $table = $this->getMock(
            '\Common\Service\Table\TableBuilder', array(
            'loadConfig',
            'loadData',
            'loadParams',
            'setupAction'
            ), array($this->getMockServiceLocator())
        );

        $table->expects($this->at(0))
            ->method('loadConfig');

        $table->expects($this->at(1))
            ->method('loadData');

        $table->expects($this->at(2))
            ->method('loadParams');

        $table->expects($this->at(3))
            ->method('setupAction');

        $this->assertEquals($table, $table->buildTable('test', array(), array(), false));
    }

    /**
     * Test loadConfig without table config set
     *
     * @expectedException \Exception
     */
    public function testLoadConfigWithoutTableConfig()
    {
        $table = new TableBuilder($this->getMockServiceLocator(false));

        $table->loadConfig('test');
    }

    /**
     * Test loadConfig with empty array
     */
    public function testLoadConfigWithEmptyArray()
    {
        $tableConfig = array(
        );

        $table = $this->getMockTableBuilder(array('getConfigFromFile'));

        $table->expects($this->once())
            ->method('getConfigFromFile')
            ->will($this->returnValue($tableConfig));

        $this->assertTrue($table->loadConfig('test'));

        $this->assertEquals(null, $table->getSetting('paginate'));

        $this->assertEquals('default', $table->getSetting('paginate', 'default'));

        $this->assertEquals(array(), $table->getAttributes());
        $this->assertEquals(array(), $table->getColumns());
        $this->assertEquals(array('hidden' => 'default'), $table->getVariables());
    }

    /**
     * Test loadConfig With pagination settings With limit
     */
    public function testLoadConfigWithPaginationWithLimit()
    {
        $paginate = array(
            'limit' => array(
                'default' => 20,
                'options' => array(
                    5, 10, 20
                )
            )
        );

        $tableConfig = array(
            'settings' => array(
                'paginate' => $paginate
            )
        );

        $table = $this->getMockTableBuilder(array('getConfigFromFile'));

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
        $paginate = array(
            'limit' => array(
                'default' => 10,
                'options' => array(
                    10, 25, 50
                )
            )
        );

        $tableConfig = array(
            'settings' => array(
                'paginate' => array()
            )
        );

        $table = $this->getMockTableBuilder(array('getConfigFromFile'));

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
        $paginate = array(
            'limit' => array(
                'default' => 10,
                'options' => array(
                    10, 25, 50
                )
            )
        );

        $tableConfig = array(
            'settings' => array(
                'paginate' => array(),
                'crud' => array(
                    'formName' => 'bob',
                    'action_field_name' => 'blah'
                )
            )
        );

        $table = $this->getMockTableBuilder(array('getConfigFromFile'));

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
        $data = array();

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->loadData($data);

        $this->assertEquals(array(), $table->getRows());

        $this->assertEquals(0, $table->getTotal());
    }

    /**
     * Test loadData with rows of data
     */
    public function testLoadDataWithDataRows()
    {
        $data = array(
            array('foo' => 'bar'),
            array('foo' => 'bar')
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->loadData($data);

        $this->assertEquals($data, $table->getRows());

        $this->assertEquals(2, $table->getTotal());
    }

    /**
     * Test loadData with result data
     */
    public function testLoadDataWithResultData()
    {
        $rows = array(
            array('foo' => 'bar'),
            array('foo' => 'bar')
        );

        $data = array(
            'Results' => $rows,
            'Count' => 10
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->loadData($data);

        $this->assertEquals($rows, $table->getRows());

        $this->assertEquals(10, $table->getTotal());
    }

    /**
     * Test loadParams Without Url
     *
     */
    public function testLoadParamsWithoutUrl()
    {
        $params = array(
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->loadParams($params);
    }

    /**
     * Test loadParams With limit
     */
    public function testLoadParamsWithLimit()
    {
        $url = new \stdClass();

        $params = array(
            'url' => $url,
            'limit' => 10
        );

        $expected = array_merge(array('page' => 1, 'sort' => '', 'order' => 'ASC'), $params);

        $table = new TableBuilder($this->getMockServiceLocator());

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

        $params = array(
            'url' => $url
        );

        $tableConfig = array(
            'variables' => array(
                'foo' => 'bar',
                'title' => 'Test',
            ),
            'settings' => array(
                'paginate' => array()
            )
        );

        $expectedVariables = $params;

        $expectedVariables['foo'] = 'bar';
        $expectedVariables['title'] = null;

        $expectedVariables['hidden'] = 'default';
        $expectedVariables['limit'] = 10;
        $expectedVariables['page'] = 1;
        $expectedVariables['sort'] = '';
        $expectedVariables['order'] = 'ASC';

        $table = $this->getMockTableBuilder(array('getConfigFromFile'));

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

        $params = array(
            'url' => 'foo',
            'query' => $query,
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->loadParams($params);

        $this->assertSame($query, $table->getQuery());
    }

    /**
     * Test setupAction with action set
     */
    public function testSetupActionWithActionSet()
    {
        $variables = array(
            'action' => '/'
        );

        $table = $this->getMockTableBuilder(array('getVariables', 'getUrl'));

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
        $variables = array();

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('/someaction'));

        $table = $this->getMockTableBuilder(array('getVariables', 'getUrl'));

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
        $variables = array(
            'action_route' => array('route' => 'someroute', 'params' => array('foo' => 'bar'))
        );

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('/someaction'));

        $table = $this->getMockTableBuilder(array('getVariables', 'getUrl'));

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

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('HTML', array())
            ->will($this->returnValue('MORE HTML'));

        $table = $this->getMockTableBuilder(array('renderTable', 'getVariables', 'getContentHelper'));

        $table->expects($this->once())
            ->method('renderTable')
            ->will($this->returnValue('HTML'));

        $table->expects($this->once())
            ->method('getVariables')
            ->will($this->returnValue(array()));

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
        $table = new TableBuilder($this->getMockServiceLocator());

        $this->assertEquals('', $table->renderTableFooter());
    }

    /**
     * Test renderTableFooter
     */
    public function testRenderTableFooter()
    {
        $footer = array(
            array(
                'type' => 'th',
                'colspan' => 2,
                'content' => 'foo',
                'formatter' => function () {
                    return 'ABC';
                }
            ),
            array(
                'format' => 'HTML'
            )
        );

        $table = $this->getMock(
            '\Common\Service\Table\TableBuilder',
            array('getContentHelper'),
            array($this->getMockServiceLocator())
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

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
        $settings = array(
            'crud' => 'foo',
            'paginate' => 'bar'
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

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
        $settings = array(
            'crud' => 'foo'
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

        $table->expects($this->once())
            ->method('setType')
            ->with(TableBuilder::TYPE_CRUD);

        $table->expects($this->once())
            ->method('renderLayout')
            ->with('crud');

        $table->setSettings($settings);

        $table->renderTable();
    }

    /**
     * Test renderTable For SubmissionSection
     */
    public function testRenderTableForSubmissionSection()
    {
        $settings = array(
            'submission_section' => 'foo'
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

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
        $settings = array(
            'crud' => 'foo'
        );

        $variables = array(
            'within_form' => true
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

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
        $settings = array(
            'paginate' => 'foo'
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

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
        $settings = array(
        );

        $table = $this->getMockTableBuilder(array('setType', 'renderLayout'));

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

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $mockContentHelper = $this->getMock('\stdClass', array('renderLayout'));

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
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setType(TableBuilder::TYPE_CRUD);

        $this->assertEquals('', $table->renderTotal());
    }

    /**
     * Test renderTotal With pagination
     */
    public function testRenderTotalWithPagination()
    {
        $total = 10;

        $expectedTotal = $total . ' results';

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(' {{[elements/total]}}', array('total' => $expectedTotal))
            ->will($this->returnValue($expectedTotal));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderTotal With pagination With 1 result
     */
    public function testRenderTotalWithPaginationWith1()
    {
        $total = 1;

        $expectedTotal = $total . ' result';

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(' {{[elements/total]}}', array('total' => $expectedTotal))
            ->will($this->returnValue($expectedTotal));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $table->setTotal($total);

        $this->assertEquals($expectedTotal, $table->renderTotal());
    }

    /**
     * Test renderActions With Pagination
     */
    public function testRenderActionsWithoutCrud()
    {
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $this->assertEquals('', $table->renderActions());
    }

    /**
     * Test renderActions without actions
     */
    public function testRenderActionsWithoutActions()
    {
        $settings = array(
            'crud' => array(
            )
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals('', $table->renderActions());
    }

    /**
     * Test renderActions with trimmed actions
     */
    public function testRenderActionsWithTrimmedActions()
    {
        $settings = array(
            'crud' => array(
                'actions' => array(
                    'add' => array('requireRows' => true)
                )
            )
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals('', $table->renderActions());
    }

    /**
     * Test renderActions
     */
    public function testRenderActions()
    {
        $settings = array(
            'crud' => array(
                'actions' => array(
                    'add' => array(),
                    'edit' => array()
                )
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

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

        $table = $this->getMockTableBuilder(array('getContentHelper', 'renderButtonActions'));

        $table->expects($this->once())
            ->method('renderButtonActions')
            ->will($this->returnValue('BUTTONS'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals(array('content' => 'BUTTONS'), $table->renderActions());
    }

    /**
     * Test renderActions With Dropdown
     */
    public function testRenderActionsWithDropdown()
    {
        $settings = array(
            'crud' => array(
                'actions' => array(
                    'add' => array(),
                    'edit' => array(),
                    'foo' => array(),
                    'bar' => array(),
                    'cake' => array()
                )
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

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

        $table = $this->getMockTableBuilder(array('getContentHelper', 'renderDropdownActions'));

        $table->expects($this->once())
            ->method('renderDropdownActions')
            ->will($this->returnValue('BUTTONS'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setType(TableBuilder::TYPE_CRUD);

        $table->setSettings($settings);

        $this->assertEquals(array('content' => 'BUTTONS'), $table->renderActions());
    }

    /**
     * Test renderAttributes
     */
    public function testRenderAttributes()
    {
        $attributes = array();

        $mockContentHelper = $this->getMock('\stdClass', array('renderAttributes'));

        $mockContentHelper->expects($this->once())
            ->method('renderAttributes')
            ->with($attributes);

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $mockContentHelper = $this->getMock('\stdClass', array('renderAttributes'));

        $mockContentHelper->expects($this->once())
            ->method('renderAttributes')
            ->with(array());

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $actions = array(
            array(
                'foo' => 'bar'
            ),
            array(
                'foo' => 'bar'
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/actionOption]}}');

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/actionOption]}}');

        $mockContentHelper->expects($this->at(2))
            ->method('replaceContent')
            ->with('{{[elements/actionSelect]}}');

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderDropdownActions($actions);
    }

    /**
     * Test renderButtonActions
     */
    public function testRenderButtonActions()
    {
        $actions = array(
            array(
                'foo' => 'bar'
            ),
            array(
                'foo' => 'bar'
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/actionButton]}}');

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/actionButton]}}');

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderButtonActions($actions);
    }

    /**
     * Test renderFooter Without pagination
     */
    public function testRenderFooterWithoutPagination()
    {
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setType(TableBuilder::TYPE_CRUD);

        $this->assertEquals('', $table->renderFooter());
    }

    /**
     * Test renderFooter without enough results
     */
    public function testRenderFooterWithoutEnoughResults()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array(10, 20, 30)
                )
            )
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setSettings($settings);

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $table->setLimit(10);

        $table->setTotal(1);

        $this->assertEquals('', $table->renderFooter());
    }

    /**
     * Test renderFooter With a custom limit
     */
    public function testRenderFooterWithCustomLimit()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array(10, 20, 30)
                )
            )
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setSettings($settings);

        $table->setType(TableBuilder::TYPE_PAGINATE);

        $table->setLimit(7);

        $table->setTotal(1);

        $this->assertEquals('', $table->renderFooter());
    }

    /**
     * Test renderFooter
     */
    public function testRenderFooter()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array(10, 20, 30)
                )
            )
        );

        $table = $this->getMockTableBuilder(array('renderLayout'));

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
     */
    public function testRenderLimitOptionsWithoutLimitOptions()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array()
                )
            )
        );

        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setSettings($settings);

        $this->assertEquals('', $table->renderLimitOptions());
    }

    /**
     * Test renderLimitOptions
     */
    public function testRenderLimitOptions()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array(
                        10, 20, 30
                    )
                )
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => 'current', 'option' => '10'));

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/limitLink]}}')
            ->will($this->returnValue('20'));

        $mockContentHelper->expects($this->at(2))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => '', 'option' => '20'));

        $mockContentHelper->expects($this->at(3))
            ->method('replaceContent')
            ->with('{{[elements/limitLink]}}')
            ->will($this->returnValue('30'));

        $mockContentHelper->expects($this->at(4))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => '', 'option' => '30'));

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $table = $this->getMockTableBuilder(array('getContentHelper', 'getUrl'));

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
     */
    public function testRenderLimitOptionsWithQueryEnabled()
    {
        $settings = array(
            'paginate' => array(
                'limit' => array(
                    'options' => array(
                        10, 20, 30
                    )
                )
            )
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => 'current', 'option' => '10'));

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/limitLink]}}')
            ->will($this->returnValue('20'));

        $mockContentHelper->expects($this->at(2))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => '', 'option' => '20'));

        $mockContentHelper->expects($this->at(3))
            ->method('replaceContent')
            ->with('{{[elements/limitLink]}}', array('option' => '30', 'link' => '?foo=bar&page=1&limit=30'))
            ->will($this->returnValue('30'));

        $mockContentHelper->expects($this->at(4))
            ->method('replaceContent')
            ->with('{{[elements/limitOption]}}', array('class' => '', 'option' => '30'));

        $mockQuery = $this->getMock('\stdClass', array('toArray'));

        $mockQuery->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $table = $this->getMockTableBuilder(array('getContentHelper', 'getQuery', 'getUrl'));

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
     * Test renderPageOptions without options
     */
    public function testRenderPageOptionsWithoutOptions()
    {
        $options = array(

        );

        $mockPaginationHelper = $this->getMock('\stdClass', array('getOptions'));

        $mockPaginationHelper->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $table = $this->getMockTableBuilder(array('getPaginationHelper'));

        $table->expects($this->once())
            ->method('getPaginationHelper')
            ->will($this->returnValue($mockPaginationHelper));

        $this->assertEquals('', $table->renderPageOptions());
    }

    /**
     * Test renderPageOptions
     */
    public function testRenderPageOptions()
    {
        $options = array(
            array(
                'page' => null,
                'label' => '...'
            ),
            array(
                'page' => 1,
                'label' => '1'
            ),
            array(
                'page' => 2,
                'label' => '2'
            )
        );

        $mockPaginationHelper = $this->getMock('\stdClass', array('getOptions'));

        $mockPaginationHelper->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/paginationItem]}}');

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/paginationLink]}}');

        $mockContentHelper->expects($this->at(2))
            ->method('replaceContent')
            ->with('{{[elements/paginationItem]}}');

        $mockContentHelper->expects($this->at(3))
            ->method('replaceContent')
            ->with('{{[elements/paginationItem]}}');

        $table = $this->getMockTableBuilder(array('getPaginationHelper', 'getUrl', 'getContentHelper'));

        $table->setPage(2);

        $table->expects($this->once())
            ->method('getPaginationHelper')
            ->will($this->returnValue($mockPaginationHelper));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('', $table->renderPageOptions());
    }

    /**
     * Test renderHeaderColumn Without options
     */
    public function testRenderHeaderColumnWithoutOptions()
    {
        $column = array(

        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/th]}}');

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column);
    }

    /**
     * Test renderHeaderColumn With custom content
     */
    public function testRenderHeaderColumnWithCustomContent()
    {
        $column = array(

        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/foo]}}');

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column, '{{[elements/foo]}}');
    }

    /**
     * Test renderHeaderColumn With sort current order asc
     */
    public function testRenderHeaderColumnWithSortCurrentOrderAsc()
    {
        $column = array(
            'sort' => 'foo'
        );

        $expectedColumn = array(
            'sort' => 'foo',
            'class' => 'sortable ascending',
            'order' => 'DESC',
            'link' => 'LINK'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn);

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/foo]}}');

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(array('getContentHelper', 'getUrl'));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('foo');
        $table->setOrder('ASC');

        $table->renderHeaderColumn($column, '{{[elements/foo]}}');
    }

    /**
     * Test renderHeaderColumn With sort current order desc
     */
    public function testRenderHeaderColumnWithSortCurrentOrderDesc()
    {
        $column = array(
            'sort' => 'foo'
        );

        $expectedColumn = array(
            'sort' => 'foo',
            'class' => 'sortable descending',
            'order' => 'ASC',
            'link' => 'LINK'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn);

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/foo]}}');

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(array('getContentHelper', 'getUrl'));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('foo');
        $table->setOrder('DESC');

        $table->renderHeaderColumn($column, '{{[elements/foo]}}');
    }

    /**
     * Test renderHeaderColumn With sort
     */
    public function testRenderHeaderColumnWithSort()
    {
        $column = array(
            'sort' => 'foo'
        );

        $expectedColumn = array(
            'sort' => 'foo',
            'class' => 'sortable',
            'order' => 'ASC',
            'link' => 'LINK'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('{{[elements/sortColumn]}}', $expectedColumn);

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/foo]}}');

        $mockUrl = $this->getMock('\stdClass', array('fromRoute'));

        $mockUrl->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('LINK'));

        $table = $this->getMockTableBuilder(array('getContentHelper', 'getUrl'));

        $table->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($mockUrl));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->setSort('bar');
        $table->setOrder('DESC');

        $table->renderHeaderColumn($column, '{{[elements/foo]}}');
    }

    /**
     * Test renderHeaderColumn With pre-set width
     */
    public function testRenderHeaderColumnWithWidthAndTitle()
    {
        $column = array(
            'width' => 'checkbox',
            'title' => 'Title',
        );

        $expectedColumn = array(
            'width' => '20px',
            'title' => null,
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/th]}}', $expectedColumn);

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderHeaderColumn($column);
    }

    /**
     * Test renderHeaderColumn when disabled
     */
    public function testRenderHeaderColumnWhenDisabled()
    {
        $column = array(
            'hideWhenDisabled' => true
        );

        $table = $this->getMockTableBuilder(array('getContentHelper'));
        $table->setDisabled(true);

        $response = $table->renderHeaderColumn($column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderBodyColumn when disabled
     */
    public function testRenderBodyColumnWhenDisabled()
    {
        $column = array(
            'hideWhenDisabled' => true
        );

        $table = $this->getMockTableBuilder(array('getContentHelper'));
        $table->setDisabled(true);

        $response = $table->renderBodyColumn([], $column);

        $this->assertEquals(null, $response);
    }

    /**
     * Test renderBodyColumn With Empty Row With Empty Column
     */
    public function testRenderBodyColumnEmptyRowEmptyColumn()
    {
        $row = array();

        $column = array();

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => ''));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'foo' => 'bar'
        );

        $column = array(
            'name' => 'foo'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => 'bar'));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn Custom Wrapper
     */
    public function testRenderBodyColumnCustomWrapper()
    {
        $row = array();

        $column = array();

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/foo]}}', array('content' => ''));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'test' => 'bar'
        );

        $column = array(
            'format' => 'FOO'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('FOO', $row)
            ->will($this->returnValue('FOOBAR'));

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => 'FOOBAR'));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'date' => date('Y-m-d')
        );

        $column = array(
            'formatter' => 'Date',
            'name' => 'date'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => date('d/m/Y')));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Invalid Formatter
     */
    public function testRenderBodyColumnWithInvalidFormatter()
    {
        $row = array(
            'date' => date('Y-m-d')
        );

        $column = array(
            'formatter' => 'Blah'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => ''));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

        $table->expects($this->any())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $table->renderBodyColumn($row, $column);
    }

    /**
     * Test renderBodyColumn With Formatter Returning Array
     */
    public function testRenderBodyColumnWithFormatterReturningArray()
    {
        $row = array(
            'date' => date('Y-m-d')
        );

        $column = array(
            'formatter' => function () {
                return array('date' => 'Something Else');
            },
            'name' => 'date'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => 'Something Else'));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'id' => 1
        );

        $column = array(
            'type' => 'Selector'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => '<input type="radio" name="id" value="1" />'));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'id' => 1
        );

        $column = array(
            'type' => 'Selector'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/td]}}', array('content' => '<input type="radio" name="table[id]" value="1" />'));

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'id' => 1,
            'foo' => 'bar'
        );

        $column = array(
            'type' => 'Action',
            'name' => 'foo',
            'class' => '',
            'action' => 'edit'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                array('content' => '<input type="submit" class="" name="action[edit][1]" value="bar" />')
            );

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $row = array(
            'id' => 1,
            'foo' => 'bar'
        );

        $column = array(
            'type' => 'Action',
            'name' => 'foo',
            'class' => '',
            'action' => 'edit'
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with(
                '{{[elements/td]}}',
                array('content' => '<input type="submit" class="" name="table[action][edit][1]" value="bar" />')
            );

        $table = $this->getMockTableBuilder(array('getContentHelper'));

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
        $table = $this->getMockTableBuilder(array('getRows'));

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue(array('foo' => 'bar')));

        $this->assertEquals('', $table->renderExtraRows());
    }

    /**
     * Test renderExtraRows without rows with custom message
     */
    public function testRenderExtraRowsWithoutRowsCustomMessage()
    {
        $table = $this->getMockTableBuilder(array('getRows', 'getColumns', 'getContentHelper', 'getServiceLocator'));

        $mockTranslator = $this->getMock('\stdClass', array('translate'));

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return $string;
                    }
                )
            );

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));

        $table->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $table->setVariables(array('empty_message' => 'Empty'));

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue(array()));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(array('foo')));

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->at(0))
            ->method('replaceContent')
            ->with('Empty')
            ->will($this->returnValue('Empty'));

        $mockContentHelper->expects($this->at(1))
            ->method('replaceContent')
            ->with('{{[elements/emptyRow]}}', array('colspan' => 1, 'message' => 'Empty'))
            ->will($this->returnValue('CONTENT'));

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
        $table = $this->getMockTableBuilder(array('getRows', 'getColumns', 'getContentHelper', 'getServiceLocator'));

        $mockTranslator = $this->getMock('\stdClass', array('translate'));

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return $string;
                    }
                )
            );

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));

        $table->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $table->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue(array()));

        $table->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(array('foo')));

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/emptyRow]}}', array('colspan' => 1, 'message' => 'The table is empty'))
            ->will($this->returnValue('CONTENT'));

        $table->expects($this->once())
            ->method('getContentHelper')
            ->will($this->returnValue($mockContentHelper));

        $this->assertEquals('CONTENT', $table->renderExtraRows());
    }

    /**
     * Test getServiceLocator method
     */
    public function testGetServiceLocator()
    {
        $tableFactory = new TableFactory();
        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));
        $tableBuilder = $tableFactory->createService($serviceLocator)->getTableBuilder();

        $newServiceLocator = $tableBuilder->getServiceLocator();

        $this->assertTrue($newServiceLocator instanceof \Zend\ServiceManager\ServiceManager);
        $this->assertTrue($newServiceLocator === $serviceLocator);
    }

    /**
     * Test action field name and fieldset
     */
    public function testActionFieldNameAndFieldset()
    {
        $actionName = 'foo';

        $fieldset = 'table';

        $table = new TableBuilder($this->getMockServiceLocator());

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
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setFooter(array('Foo' => 'Bar'));

        $this->assertEquals(array('Foo' => 'Bar'), $table->getFooter());
    }

    /**
     * Test get and set variable
     */
    public function testGetVariable()
    {
        $table = new TableBuilder($this->getMockServiceLocator());

        $vars = array(
            'foo' => 'bar',
            'bar' => 'cake'
        );

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
        $table = new TableBuilder($this->getMockServiceLocator());

        $columns = array(
            array('name' => 'name1'),
            array('name' => 'name2')
        );

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
        $table = new TableBuilder($this->getMockServiceLocator());
        $columns = array(
            array('name' => 'name1'),
            array('foo' => 'bar')
        );
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
        $table = new TableBuilder($this->getMockServiceLocator());

        $table->setSettings(array('Foo' => 'Bar'));

        $this->assertEquals(array('Foo' => 'Bar'), $table->getSettings());
    }
}
