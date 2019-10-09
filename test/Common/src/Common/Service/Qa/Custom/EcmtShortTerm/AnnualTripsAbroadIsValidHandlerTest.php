<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadIsValidHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * AnnualTripsAbroadIsValidHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadIsValidHandlerTest extends MockeryTestCase
{
    /**
     * @dataProvider dpIsValid
     */
    public function testIsValid($permitsRequired, $intensityWarningThreshold, $warningVisibleValue, $expectedIsValid)
    {
        $applicationStep = [
            'element' => [
                'intensityWarningThreshold' => $intensityWarningThreshold
            ]
        ];

        $questionFieldsetData = [
            'qaElement' => $permitsRequired,
            'warningVisible' => $warningVisibleValue
        ];

        $qaForm = m::mock(QaForm::class);
        $qaForm->shouldReceive('getApplicationStep')
            ->andReturn($applicationStep);
        $qaForm->shouldReceive('getQuestionFieldsetData')
            ->andReturn($questionFieldsetData);

        $annualTripsAbroadIsValidHandler = new AnnualTripsAbroadIsValidHandler();

        $this->assertEquals(
            $expectedIsValid,
            $annualTripsAbroadIsValidHandler->isValid($qaForm)
        );
    }

    public function dpIsValid()
    {
        return [
            [4, 5, 1, true],
            [5, 5, 1, true],
            [6, 5, 1, true],
            [4, 5, 0, true],
            [5, 5, 0, true],
            [6, 5, 0, false],
        ];
    }
}
