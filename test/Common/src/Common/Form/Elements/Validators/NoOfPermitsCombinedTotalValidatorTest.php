<?php

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\NoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * NoOfPermitsCombinedTotalValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsCombinedTotalValidatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpValidateNonZeroValuePresentSuccess
     */
    public function testValidateNonZeroValuePresentSuccess($context)
    {
        $this->assertTrue(
            NoOfPermitsCombinedTotalValidator::validateNonZeroValuePresent(null, $context)
        );
    }

    public function dpValidateNonZeroValuePresentSuccess()
    {
        return [
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => '7']],
            [['submit' => 'xyz', 'requiredEuro5' => '3', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => '', 'requiredEuro6' => '1']],
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => '']],
            [['submit' => 'xyz', 'requiredEuro5' => 'foo', 'requiredEuro6' => '1']],
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => 'bar']],
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => '4']],
            [['submit' => 'xyz', 'requiredEuro5' => '1']],
            [['submit' => 'xyz', 'requiredEuro6' => '8']],
        ];
    }

    /**
     * @dataProvider dpValidateNonZeroValuePresentFailure
     */
    public function testValidateNonZeroValuePresentFailure($context)
    {
        $this->assertFalse(
            NoOfPermitsCombinedTotalValidator::validateNonZeroValuePresent(null, $context)
        );
    }

    public function dpValidateNonZeroValuePresentFailure()
    {
        return [
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => 'foo', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => '', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => 'bar']],
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => '']],
            [['submit' => 'xyz', 'requiredEuro5' => '']],
            [['submit' => 'xyz', 'requiredEuro5' => 'bar']],
            [['submit' => 'xyz', 'requiredEuro6' => '0']],
        ];
    }

    /**
     * @dataProvider dpValidateMultipleNonZeroValuesNotPresentSuccess
     */
    public function testValidateMultipleNonZeroValuesNotPresentSuccess($context)
    {
        $this->assertTrue(
            NoOfPermitsCombinedTotalValidator::validateMultipleNonZeroValuesNotPresent(null, $context)
        );
    }

    public function dpValidateMultipleNonZeroValuesNotPresentSuccess()
    {
        return [
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => 'foo', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => '0', 'requiredEuro6' => '7']],
            [['submit' => 'xyz', 'requiredEuro5' => '3', 'requiredEuro6' => '0']],
            [['submit' => 'xyz', 'requiredEuro5' => '', 'requiredEuro6' => '1']],
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => '']],
            [['submit' => 'xyz', 'requiredEuro5' => 'foo', 'requiredEuro6' => '1']],
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => 'bar']],
            [['submit' => 'xyz', 'requiredEuro5' => '1']],
            [['submit' => 'xyz', 'requiredEuro6' => '8']],
        ];
    }

    /**
     * @dataProvider dpValidateMultipleNonZeroValuesNotPresentFailure
     */
    public function testValidateMultipleNonZeroValuesNotPresentFailure($context)
    {
        $this->assertFalse(
            NoOfPermitsCombinedTotalValidator::validateMultipleNonZeroValuesNotPresent(null, $context)
        );
    }

    public function dpValidateMultipleNonZeroValuesNotPresentFailure()
    {
        return [
            [['submit' => 'xyz', 'requiredEuro5' => '2', 'requiredEuro6' => '7']],
            [['submit' => 'xyz', 'requiredEuro5' => '7', 'requiredEuro6' => '2']],
            [['submit' => 'xyz', 'requiredEuro5' => '1', 'requiredEuro6' => '1']],
        ];
    }
}
