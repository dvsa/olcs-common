<?php

/**
 * Stack Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\StackHelperService;

/**
 * Stack Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\StackHelperService
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new StackHelperService();
    }

    /**
     * @dataProvider providerGetStackValue
     */
    public function testGetStackValue($stack, $stackReference, $expected)
    {
        $this->assertEquals($expected, $this->sut->getStackValue($stack, $stackReference));
    }

    public function providerGetStackValue()
    {
        return [
            'top level' => [
                ['foo' => 'bar'],
                ['foo'],
                'bar'
            ],
            'nested top level' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo'],
                ['bar' => ['cake' => 'baz']]
            ],
            'nested mid level' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo', 'bar'],
                ['cake' => 'baz']
            ],
            'nested deepest level' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo', 'bar', 'cake'],
                'baz'
            ],
            'missing reference 1' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo', 'bar', 'cake', 'foo'],
                null
            ],
            'missing reference 2' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo', 'baz', 'cake', 'foo'],
                null
            ],
            'missing reference 3' => [
                ['foo' => ['bar' => ['cake' => 'baz']]],
                ['foo', 'baz'],
                null
            ]
        ];
    }
}
