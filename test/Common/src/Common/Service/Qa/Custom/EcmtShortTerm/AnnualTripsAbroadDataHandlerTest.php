<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadDataHandler;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadIsValidHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Helper\Partial;
use Zend\Form\Element\Hidden;

/**
 * AnnualTripsAbroadDataHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadDataHandlerTest extends MockeryTestCase
{
    private $qaForm;

    private $partial;

    private $annualTripsAbroadDataHandler;

    public function setUp()
    {
        $this->qaForm = m::mock(QaForm::class);

        $this->partial = m::mock(Partial::class);

        $this->annualTripsAbroadIsValidHandler = m::mock(AnnualTripsAbroadIsValidHandler::class);

        $this->annualTripsAbroadDataHandler = new AnnualTripsAbroadDataHandler(
            $this->partial,
            $this->annualTripsAbroadIsValidHandler
        );
    }

    public function testSetDataWrongDataValues()
    {
        $this->annualTripsAbroadIsValidHandler->shouldReceive('isValid')
            ->andReturn(true);

        $this->annualTripsAbroadDataHandler->setData($this->qaForm);
    }

    public function testSetDataModifyForm()
    {
        $this->annualTripsAbroadIsValidHandler->shouldReceive('isValid')
            ->andReturn(false);

        $intensityWarningMarkup = '<h1>intensity warning markup</h1>';

        $this->partial->shouldReceive('__invoke')
            ->with(
                'partials/warning-component',
                ['translationKey' => 'permits.form.trips.warning']
            )
            ->once()
            ->andReturn($intensityWarningMarkup);

        $intensityWarningElementParams = [
            'name' => 'intensityWarning',
            'type' => Html::class,
            'attributes' => [
                'value' => $intensityWarningMarkup
            ]
        ];

        $intensityWarningFlagsParams = [
            'priority' => 10
        ];

        $warningVisibleElement = m::mock(Hidden::class);
        $warningVisibleElement->shouldReceive('setValue')
            ->with(1)
            ->once();

        $questionFieldset = m::mock(Fieldset::class);
        $questionFieldset->shouldReceive('get')
            ->with('warningVisible')
            ->andReturn($warningVisibleElement);
        $questionFieldset->shouldReceive('add')
            ->with($intensityWarningElementParams, $intensityWarningFlagsParams)
            ->once();

        $this->qaForm->shouldReceive('getQuestionFieldset')
            ->andReturn($questionFieldset);

        $this->annualTripsAbroadDataHandler->setData($this->qaForm);
    }
}
