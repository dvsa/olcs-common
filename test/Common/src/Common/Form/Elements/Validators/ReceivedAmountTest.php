<?php

/**
 * Received Amount Validator Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\ReceivedAmount as Sut;

/**
 * Received Amount Validator Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReceivedAmountTest extends \PHPUnit\Framework\TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();
    }

    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid($value, $context, $expected)
    {
        $this->assertEquals($expected, $this->sut->isValid($value, $context));
    }

    public function isValidProvider()
    {
        return [
            [
                '0',
                null,
                true,
            ],
            [
                '100',
                null,
                true,
            ],
            [
                '100',
                [
                    'minAmountForValidator' => '10',
                ],
                true,
            ],
            [
                '10',
                [
                    'minAmountForValidator' => '10',
                ],
                true,
            ],
            [
                '9.99',
                [
                    'minAmountForValidator' => '10',
                ],
                false,
            ],
        ];
    }
}
