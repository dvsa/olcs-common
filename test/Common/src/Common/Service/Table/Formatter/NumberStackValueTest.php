<?php

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\NumberStackValue;
use Common\Service\Table\Formatter\StackValue;
use CommonTest\Bootstrap;
use Common\Service\Helper\StackHelperService;

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NumberStackValueTest extends \PHPUnit\Framework\TestCase
{
    public function testFormatWithoutStack()
    {
        $this->expectException('\InvalidArgumentException');
        $data = [];
        $column = [];
        $sm = Bootstrap::getServiceManager();

        StackValue::format($data, $column, $sm);
    }

    public function testWithThousandFormatter()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'cake' => 12300
                ]
            ]
        ];
        $column = [
            'stack' => 'foo->bar->cake'
        ];
        $expected = '12,300';
        $sm = Bootstrap::getServiceManager();

        // @NOTE We use the real stack helper here, as it's a useful component test
        // and is only a tiny utility class that is also fully tested elsewhere
        $sm->setService('Helper\Stack', new StackHelperService());

        $this->assertEquals($expected, NumberStackValue::format($data, $column, $sm));
    }
}
