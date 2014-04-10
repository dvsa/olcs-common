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
    /**+
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
        $table = new TableBuilder(array('tables' => array('config' => __DIR__ . '/TestResources')));

        $config = $table->getConfigFromFile(__DIR__ . '/TestResources/sample.table.php');

        $this->assertEquals(array('foo' => 'bar'), $config);
    }

    /**
     * Test buildTable with missing config
     *
     * @expectedException \Exception
     */
    public function testBuildTableWithMissingConfig()
    {
        $table = new TableBuilder(array());

        $table->buildTable('irrelevant');
    }

    /**
     * Test buildTable with config without table config
     *
     * @expectedException \Exception
     */
    public function testBuildTableWithConfigWithoutTableConfig()
    {
        $table = new TableBuilder(array('tables' => array('config' => '')));

        $table->buildTable('MISSING_FILE');
    }
}
