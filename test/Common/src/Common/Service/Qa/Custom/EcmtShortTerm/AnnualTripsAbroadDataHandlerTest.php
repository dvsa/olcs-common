<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadDataHandler;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadIsValidHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * AnnualTripsAbroadDataHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadDataHandlerTest extends MockeryTestCase
{
    public function testSetData()
    {
        $qaForm = m::mock(QaForm::class);

        $annualTripsAbroadIsValidHandler = m::mock(AnnualTripsAbroadIsValidHandler::class);

        $isValidBasedWarningAdder = m::mock(IsValidBasedWarningAdder::class);
        $isValidBasedWarningAdder->shouldReceive('add')
            ->with($annualTripsAbroadIsValidHandler, $qaForm, 'permits.form.trips.warning')
            ->once();

        $annualTripsAbroadDataHandler = new AnnualTripsAbroadDataHandler(
            $isValidBasedWarningAdder,
            $annualTripsAbroadIsValidHandler
        );

        $annualTripsAbroadDataHandler->setData($qaForm);
    }
}
