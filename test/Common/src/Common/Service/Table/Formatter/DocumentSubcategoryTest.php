<?php

/**
 * Document subcategory formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DocumentSubcategory;

/**
 * Document subcategory formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentSubcategoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     * @group DocumentSubcategoryFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, DocumentSubcategory::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                array(
                    'documentSubCategoryName' => 'foo',
                    'isExternal' => false,
                    'ciId' => null
                ),
                'foo'
            ),
            array(
                array(
                    'documentSubCategoryName' => 'foo',
                    'isExternal' => true,
                    'ciId' => null
                ),
                'foo (selfserve)'
            ),
            array(
                array(
                    'documentSubCategoryName' => 'foo',
                    'isExternal' => true,
                    'ciId' => 123
                ),
                'foo (selfserve) (emailed)'
            )
        );
    }
}
