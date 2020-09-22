<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Ecmt\NoOfPermitsBothValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsBothValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsBothValidatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpIsValid
     */
    public function testIsValid(
        $value,
        $permitsRemaining,
        $emissionsCategory,
        $expectedMessages,
        $expectedIsValid
    ) {
        $options = [
            'permitsRemaining' => $permitsRemaining,
            'emissionsCategory' => $emissionsCategory,
        ];

        $noOfPermitsBothValidator = new NoOfPermitsBothValidator($options);

        $this->assertEquals(
            $expectedIsValid,
            $noOfPermitsBothValidator->isValid($value)
        );

        $this->assertEquals(
            $expectedMessages,
            $noOfPermitsBothValidator->getMessages()
        );
    }

    public function dpIsValid()
    {
        return [
            'valid' => [
                '6',
                6,
                'euro5',
                [],
                true
            ],
            'not valid (euro5)' => [
                '7',
                6,
                'euro5',
                [
                    NoOfPermitsBothValidator::PERMITS_REMAINING_THRESHOLD =>
                        'qanda.ecmt.number-of-permits.error.permits-remaining-exceeded.euro5'
                ],
                false
            ],
            'not valid (euro6)' => [
                '7',
                6,
                'euro6',
                [
                    NoOfPermitsBothValidator::PERMITS_REMAINING_THRESHOLD =>
                        'qanda.ecmt.number-of-permits.error.permits-remaining-exceeded.euro6'
                ],
                false
            ],
        ];
    }
}
