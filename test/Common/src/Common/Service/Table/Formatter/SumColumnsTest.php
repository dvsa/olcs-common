<?php

/**
 * Sum Columns formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\SumColumns;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Sum Columns formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SumColumnsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group SumColumnsFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->createPartialMock(Translator::class, array('translate'));

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));
        $this->assertSame($expected, SumColumns::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            [[], [], '0'],
            [['a' => 1, 'b' => 2], ['columns' => ['a', 'b']], '3'],
            [['a' => 1, 'b' => 2], ['columns' => ['a']], '1'],
            [['a' => 1], ['columns' => ['b']], '0'],
        ];
    }
}
