<?php

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase;
use Common\Service\Table\Formatter\StackValue;
use CommonTest\Bootstrap;
use Common\Service\Helper\StackHelperService;

/**
 * StackValue formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackValueTest extends PHPUnit_Framework_TestCase
{
    public function testFormatWithoutStack()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $data = [];
        $column = [];
        $sm = Bootstrap::getServiceManager();

        StackValue::format($data, $column, $sm);
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
        $sm = Bootstrap::getServiceManager();

        // @NOTE We use the real stack helper here, as it's a useful component test
        // and is only a tiny utility class that is also fully tested elsewhere
        $sm->setService('Helper\Stack', new StackHelperService());

        $this->assertEquals($expected, StackValue::format($data, $column, $sm));
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
        $sm = Bootstrap::getServiceManager();

        // @NOTE We use the real stack helper here, as it's a useful component test
        // and is only a tiny utility class that is also fully tested elsewhere
        $sm->setService('Helper\Stack', new StackHelperService());

        $this->assertEquals($expected, StackValue::format($data, $column, $sm));
    }
}
