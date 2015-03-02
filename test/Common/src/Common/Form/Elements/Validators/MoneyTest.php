<?php

/**
 * Money Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\Money;

/**
 * Money Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MoneyTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Money();
    }

    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid($value, $expected)
    {
        $this->assertEquals($expected, $this->sut->isValid($value));
    }

    public function isValidProvider()
    {
        return [
            [
                'abc',
                false
            ],
            [
                'abc123',
                false
            ],
            [
                '123',
                true
            ],
            [
                123,
                true
            ],
            [
                '123.45',
                true
            ],
            [
                123.45,
                true
            ],
            [
                '123.4',
                true
            ],
            [
                123.4,
                true
            ],
            [
                '123.456',
                false
            ],
            [
                123.456,
                false
            ]
        ];
    }
}
