<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * NoOfPermitsCombinedTotalValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsCombinedTotalValidatorTest extends MockeryTestCase
{
    public function testValidateMinTrue()
    {
        $context = [
            'requiredEuro5' => null,
            'requiredEuro6' => '0',
            'otherField1' => '26'
        ];

        $this->assertFalse(NoOfPermitsCombinedTotalValidator::validateMin(3, $context));
    }

    public function testValidateMinFalse()
    {
        $context = [
            'requiredEuro5' => '0',
            'requiredEuro6' => '1',
            'otherField' => '26'
        ];

        $this->assertTrue(NoOfPermitsCombinedTotalValidator::validateMin(3, $context));
    }

    public function testValidateMaxTrue()
    {
        $context = [
            'requiredEuro5' => '3',
            'requiredEuro6' => '2',
            'otherField' => '26'
        ];

        $this->assertTrue(NoOfPermitsCombinedTotalValidator::validateMax(3, $context, 5));
    }

    public function testValidateMaxFalse()
    {
        $context = [
            'requiredEuro5' => '3',
            'requiredEuro6' => '2',
            'otherField' => '26'
        ];

        $this->assertFalse(NoOfPermitsCombinedTotalValidator::validateMax(3, $context, 4));
    }
}
