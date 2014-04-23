<?php

/**
 * Table Builder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;

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
        $applicationConfig = array(
            'tables' => array('config' => __DIR__ . '/TestResources/')
        );

        return $this->getMock('\Common\Service\Table\TableBuilder', $methods, array($applicationConfig));
    }

    /**
     * Test getContentHelper
     */
    public function testGetContentHelper()
    {
        $table = new TableBuilder(array('tables' => array('partials' => '')));

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
        $table = new TableBuilder(array());

        $table->getContentHelper();
    }

    /**
     * Test getPaginationHelper
     */
    public function testGetPaginationHelper()
    {
        $table = new TableBuilder();

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
        $table = new TableBuilder(array('tables' => array('config' => __DIR__ . '/TestResources/')));

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
        $table = new TableBuilder(array('tables' => array('config' => __DIR__ . '/TestResources/')));

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
            )
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
     * Test loadConfig without table config set
     *
     * @expectedException \Exception
     */
    public function testLoadConfigWithoutTableConfig()
    {
        $table = new TableBuilder();

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
        $this->assertEquals(array(), $table->getVariables());
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
     * Test loadData without data
     */
    public function testLoadDataWithoutData()
    {
        $data = array();

        $table = new TableBuilder();

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

        $table = new TableBuilder();

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

        $table = new TableBuilder();

        $table->loadData($data);

        $this->assertEquals($rows, $table->getRows());

        $this->assertEquals(10, $table->getTotal());
    }

    /**
     * Test loadParams Without Url
     *
     * @expectedException \Exception
     */
    public function testLoadParamsWithoutUrl()
    {
        $params = array(
        );

        $table = new TableBuilder();

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

        $table = new TableBuilder();

        $table->loadParams($params);

        $this->assertSame($url, $table->getUrl());

        $this->assertEquals(10, $table->getLimit());
        $this->assertEquals('', $table->getSort());
        $this->assertEquals('ASC', $table->getOrder());
        $this->assertEquals($params, $table->getVariables());
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
                'foo' => 'bar'
            ),
            'settings' => array(
                'paginate' => array()
            )
        );

        $expectedVariables = $params;

        $expectedVariables['foo'] = 'bar';

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
        $table = new TableBuilder();

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
                'formatter' => function ($data) {
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
            array(array('tables' => array('partials' => __DIR__ . '/TestResources/')))
        );

        $mockContentHelper = $this->getMock('\stdClass', array('replaceContent'));

        $mockContentHelper->expects($this->any())
            ->method('replaceContent')
            ->will(
                $this->returnCallback(
                    function ($string, $vars) {
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
        $table = new TableBuilder();

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
        $table = new TableBuilder();

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

        $table = new TableBuilder();

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

        $table = new TableBuilder();

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
                    'bar' => array()
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
}
