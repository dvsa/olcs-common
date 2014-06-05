<?php

/**
 * Pagination Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Table\PaginationHelper;

/**
 * Pagination Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaginationHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test paginationHelper
     *
     * @dataProvider provider
     */
    public function testPaginationHelper($page, $total, $limit, $expected)
    {
        $paginationHelper = new PaginationHelper($page, $total, $limit);

        $options = $paginationHelper->getOptions();

        $labels = array();

        foreach ($options as $option) {
            $labels[] = $option['label'];
        }

        $this->assertEquals($expected, $labels);
    }

    /**
     * Provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(1, 10, 10, array('1')),
            array(1, 50, 10, array('1', '2', '3', '4', '5', 'Next')),
            array(2, 50, 10, array('Previous', '1', '2', '3', '4', '5', 'Next')),
            array(20, 1000, 10, array('Previous', '1', '...', '18', '19', '20', '21', '22', '...', '100', 'Next')),
            array(100, 1000, 10, array('Previous', '1', '...', '96', '97', '98', '99', '100'))
        );
    }
}
