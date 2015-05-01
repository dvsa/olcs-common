<?php

/**
 * StackValueReplacer formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase;
use Common\Service\Table\Formatter\StackValueReplacer;
use CommonTest\Bootstrap;
use Common\Service\Helper\StackHelperService;

/**
 * StackValueReplacer formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackValueReplacerTest extends PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'cake' => 123
                ],
                'carrot' => 'cake'
            ]
        ];
        $column = [
            'stringFormat' => '{foo->bar->cake} {foo->carrot}(s)'
        ];
        $expected = '123 cake(s)';
        $sm = Bootstrap::getServiceManager();

        // @NOTE We use the real stack helper here, as it's a useful component test
        // and is only a tiny utility class that is also fully tested elsewhere
        $sm->setService('Helper\Stack', new StackHelperService());

        $this->assertEquals($expected, StackValueReplacer::format($data, $column, $sm));
    }
}
