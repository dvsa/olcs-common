<?php

namespace CommonTest\Service\Table;

use Common\Service\Table\PaginationHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers \Common\Service\Table\PaginationHelper
 */
class PaginationHelperTest extends MockeryTestCase
{
    /**
     * Test paginationHelper
     *
     * @dataProvider provider
     */
    public function testPaginationHelper($page, $total, $limit, $isSetTranslator, $expected)
    {
        $mockTranslator = m::mock(\Laminas\Mvc\I18n\Translator::class);
        $mockTranslator
            ->shouldReceive('translate')->with('pagination.next')->andReturn('TRNSLT_NEXT')
            ->shouldReceive('translate')->with('pagination.previous')->andReturn('TRNSLT_PREV');

        $paginationHelper = new PaginationHelper($page, $total, $limit);
        if ($isSetTranslator) {
            $paginationHelper->setTranslator($mockTranslator);
        }

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
        return [
            [
                'page' => 1,
                'total' => 10,
                'limit' => 10,
                'isSetTranslator' => false,
                'expect' => ['1'],
            ],
            [
                'page' => 1,
                'total' => 50,
                'limit' => 10,
                'isSetTranslator' => true,
                'expect' => ['1', '2', '3', '4', '5', 'TRNSLT_NEXT'],
            ],
            [
                'page' => 2,
                'total' => 50,
                'limit' => 10,
                'isSetTranslator' => true,
                'expect' => ['TRNSLT_PREV', '1', '2', '3', '4', '5', 'TRNSLT_NEXT'],
            ],
            [
                'page' => 20,
                'total' => 1000,
                'limit' => 10,
                'isSetTranslator' => false,
                'expect' => ['Previous', '1', '...', '18', '19', '20', '21', '22', '...', '100', 'Next'],
            ],
            [
                'page' => 100,
                'total' => 1000,
                'limit' => 10,
                'isSetTranslator' => true,
                'expect' => ['TRNSLT_PREV', '1', '...', '96', '97', '98', '99', '100'],
            ],
        ];
    }
}
