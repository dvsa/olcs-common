<?php

/**
 * Test No of permits min validator
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\NoOfPermitsMin;
use Zend\Validator\GreaterThan;

/**
 * Test No of permits min validator
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsMinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up the validator
     */
    public function setUp(): void
    {
        $this->validator = new NoOfPermitsMin();
    }

    /**
     * Test isValid
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($value, null));
    }

    public function testMessageTemplates()
    {
        $expectedValue = [
            GreaterThan::NOT_GREATER_INCLUSIVE => 'permits.page.no-of-permits.error.general'
        ];

        $this->assertEquals(
            $expectedValue,
            $this->validator->getMessageTemplates()
        );
    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return [
            [0, true],
            [1, true],
            [-1, false],
            [2, true],
            [999, true]
        ];
    }
}
