<?php

/**
 * Cheque Date Validator Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\ChequeDate;

/**
 * Cheque Date Validator Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ChequeDateTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->sut = new ChequeDate();
    }

    /**
     * @group validators
     * @group date_validators
     * @dataProvider providerIsValid
     */
    public function testIsValid($input, $expected)
    {
        $this->assertEquals($expected, $this->sut->isValid($input));
    }

    public function providerIsValid()
    {
        return array(
            array(
                date('Y-m-d'),
                true
            ),
            array(
                date('Y-m-d', strtotime('-1 month')),
                true
            ),
            array(
                date('Y-m-d', strtotime('-6 months')),
                true
            ),
            array(
                date('Y-m-d', strtotime('-7 months')),
                false
            )
        );
    }
}
