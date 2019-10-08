<?php

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\EcmtAnnualNoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * EcmtAnnualNoOfPermitsCombinedTotalValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtAnnualNoOfPermitsCombinedTotalValidatorTest extends MockeryTestCase
{
    public function testValidateMinTrue()
    {
        $context = [
            'requiredEuro5' => null,
            'requiredEuro6' => '0',
            'otherField1' => '26'
        ];

        $this->assertFalse(EcmtAnnualNoOfPermitsCombinedTotalValidator::validateMin(3, $context));
    }

    public function testValidateMinFalse()
    {
        $context = [
            'requiredEuro5' => '0',
            'requiredEuro6' => '1',
            'otherField' => '26'
        ];

        $this->assertTrue(EcmtAnnualNoOfPermitsCombinedTotalValidator::validateMin(3, $context));
    }

    public function testValidateMaxTrue()
    {
        $context = [
            'requiredEuro5' => '3',
            'requiredEuro6' => '2',
            'otherField' => '26'
        ];

        $this->assertTrue(EcmtAnnualNoOfPermitsCombinedTotalValidator::validateMax(3, $context, 5));
    }

    public function testValidateMaxFalse()
    {
        $context = [
            'requiredEuro5' => '3',
            'requiredEuro6' => '2',
            'otherField' => '26'
        ];

        $this->assertFalse(EcmtAnnualNoOfPermitsCombinedTotalValidator::validateMax(3, $context, 4));
    }
}
