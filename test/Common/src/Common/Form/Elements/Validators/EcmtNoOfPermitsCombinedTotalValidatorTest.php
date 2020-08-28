<?php

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\EcmtNoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EcmtNoOfPermitsCombinedTotalValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsCombinedTotalValidatorTest extends MockeryTestCase
{
    public function testValidateMinTrue()
    {
        $context = [
            'euro5' => null,
            'euro6' => '0',
            'otherField1' => '26'
        ];

        $this->assertFalse(
            EcmtNoOfPermitsCombinedTotalValidator::validateMin(3, $context)
        );
    }

    public function testValidateMinFalse()
    {
        $context = [
            'euro5' => '0',
            'euro6' => '1',
            'otherField' => '26'
        ];

        $this->assertTrue(
            EcmtNoOfPermitsCombinedTotalValidator::validateMin(3, $context)
        );
    }

    public function testValidateMaxTrue()
    {
        $context = [
            'euro5' => '3',
            'euro6' => '2',
            'otherField' => '26'
        ];

        $this->assertTrue(
            EcmtNoOfPermitsCombinedTotalValidator::validateMax(3, $context, 5)
        );
    }

    public function testValidateMaxFalse()
    {
        $context = [
            'euro5' => '3',
            'euro6' => '2',
            'otherField' => '26'
        ];

        $this->assertFalse(
            EcmtNoOfPermitsCombinedTotalValidator::validateMax(3, $context, 4)
        );
    }
}
