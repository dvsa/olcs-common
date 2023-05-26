<?php

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\StackHelperService;
use Common\Service\Table\Formatter\StackValue;

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackValueTest extends \PHPUnit\Framework\TestCase
{
    public function testFormatWithoutStack()
    {
        $this->expectException('\InvalidArgumentException');
        $data = [];
        $column = [];

        (new StackValue(new StackHelperService()))->format($data, $column);
    }

    public function testFormatWithString()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'cake' => 123
                ]
            ]
        ];
        $column = [
            'stack' => 'foo->bar->cake'
        ];
        $expected = 123;

        $this->assertEquals($expected, (new StackValue(new StackHelperService()))->format($data, $column));
    }

    public function testFormat()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'cake' => 123
                ]
            ]
        ];
        $column = [
            'stack' => ['foo', 'bar', 'cake']
        ];
        $expected = 123;

        $this->assertEquals($expected, (new StackValue(new StackHelperService()))->format($data, $column));
    }
}
