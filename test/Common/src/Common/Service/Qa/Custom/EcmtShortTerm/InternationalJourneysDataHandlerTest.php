<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysDataHandler;
use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysIsValidHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * InternationalJourneysDataHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InternationalJourneysDataHandlerTest extends MockeryTestCase
{
    public function testSetData()
    {
        $qaForm = m::mock(QaForm::class);

        $internationalJourneysIsValidHandler = m::mock(InternationalJourneysIsValidHandler::class);

        $isValidBasedWarningAdder = m::mock(IsValidBasedWarningAdder::class);
        $isValidBasedWarningAdder->shouldReceive('add')
            ->with($internationalJourneysIsValidHandler, $qaForm, 'permits.form.trips.warning', 20)
            ->once();

        $internationalJourneysDataHandler = new InternationalJourneysDataHandler(
            $isValidBasedWarningAdder,
            $internationalJourneysIsValidHandler
        );

        $internationalJourneysDataHandler->setData($qaForm);
    }
}
