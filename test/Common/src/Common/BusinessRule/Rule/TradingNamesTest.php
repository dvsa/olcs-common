<?php

/**
 * Trading Names Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use PHPUnit_Framework_TestCase;
use Common\BusinessRule\Rule\TradingNames;

/**
 * Trading Names Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TradingNamesTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TradingNames();
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($tradingNames, $orgId, $licenceId, $expected)
    {
        $this->assertEquals($expected, $this->sut->validate($tradingNames, $orgId, $licenceId));
    }

    public function testFilter()
    {
        $tradingNames = [
            'foo',
            'bar',
            'untrimmed     ',
            '',
            ''
        ];

        $expected = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'untrimmed']
        ];

        $filtered = $this->sut->filter($tradingNames);

        $this->assertEquals($expected, $filtered);
    }

    public function providerValidate()
    {
        return [
            [
                [
                    'foo',
                    'bar'
                ],
                111,
                222,
                [
                    'organisation' => 111,
                    'licence' => 222,
                    'tradingNames' => [
                        'foo',
                        'bar'
                    ]
                ]
            ]
        ];
    }
}
