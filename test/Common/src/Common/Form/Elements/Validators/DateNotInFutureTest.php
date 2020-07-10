<?php

/**
 * Date Not In Future Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\DateNotInFuture;

/**
 * Date Not In Future Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateNotInFutureTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->sut = new DateNotInFuture();
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
                date('Y-m-d', strtotime('+1 day')),
                false
            ),
            array(
                date('Y-m-d', strtotime('-1 day')),
                true
            )
        );
    }
}
