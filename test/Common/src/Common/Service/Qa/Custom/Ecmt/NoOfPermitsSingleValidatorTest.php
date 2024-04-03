<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Ecmt\NoOfPermitsSingleValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsSingleValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsSingleValidatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpIsValid
     */
    public function testIsValid(
        $value,
        $permitsRemaining,
        $maxPermitted,
        $emissionsCategory,
        $expectedMessages,
        $expectedIsValid
    ): void {
        $options = [
            'permitsRemaining' => $permitsRemaining,
            'maxPermitted' => $maxPermitted,
            'emissionsCategory' => $emissionsCategory,
        ];

        $noOfPermitsSingleValidator = new NoOfPermitsSingleValidator($options);

        $this->assertEquals(
            $expectedIsValid,
            $noOfPermitsSingleValidator->isValid($value)
        );

        $this->assertEquals(
            $expectedMessages,
            $noOfPermitsSingleValidator->getMessages()
        );
    }

    public function dpIsValid()
    {
        return [
            'permits remaining less than max permitted, valid' => [
                '6',
                6,
                7,
                'euro5',
                [],
                true
            ],
            'permits remaining less than max permitted, not valid (euro5)' => [
                '7',
                6,
                7,
                'euro5',
                [
                    NoOfPermitsSingleValidator::PERMITS_REMAINING_THRESHOLD =>
                        'qanda.ecmt.number-of-permits.error.permits-remaining-exceeded.euro5'
                ],
                false
            ],
            'permits remaining less than max permitted, not valid (euro6)' => [
                '7',
                6,
                7,
                'euro6',
                [
                    NoOfPermitsSingleValidator::PERMITS_REMAINING_THRESHOLD =>
                        'qanda.ecmt.number-of-permits.error.permits-remaining-exceeded.euro6'
                ],
                false
            ],
            'max permitted less than permits remaining, valid' => [
                '6',
                7,
                6,
                'euro5',
                [],
                true
            ],
            'max permitted less than permits remaining, not valid' => [
                '7',
                7,
                6,
                'euro5',
                [
                    NoOfPermitsSingleValidator::MAX_PERMITTED_THRESHOLD =>
                        'qanda.ecmt.number-of-permits.error.total-max-exceeded'
                ],
                false
            ],
        ];
    }
}
