<?php

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\EcmtCandidatePermitSelectionValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EcmtCandidatePermitSelectionValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtCandidatePermitSelectionValidatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpValidate
     */
    public function testValidate($firstValue, $secondValue, $thirdValue, $expected)
    {
        $context = [
            'candidate-123' => $firstValue,
            'otherField1' => '0',
            'candidate-456' => $secondValue,
            'otherField2' => '1',
            'candidate-789' => $thirdValue,
        ];

        $this->assertEquals(
            $expected,
            EcmtCandidatePermitSelectionValidator::validate('notused', $context)
        );
    }

    public function dpValidate()
    {
        return [
            ['0', '0', '0', false],
            ['1', '0', '1', true],
            ['1', '1', '1', true],
        ];
    }
}
