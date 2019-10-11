<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysDataHandler;
use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysIsValidHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Helper\Partial;
use Zend\Form\Element\Hidden;

/**
 * InternationalJourneysDataHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InternationalJourneysDataHandlerTest extends MockeryTestCase
{
    private $qaForm;

    private $partial;

    private $internationalJourneysDataHandler;

    public function setUp()
    {
        $this->qaForm = m::mock(QaForm::class);

        $this->partial = m::mock(Partial::class);

        $this->internationalJourneysIsValidHandler = m::mock(InternationalJourneysIsValidHandler::class);

        $this->internationalJourneysDataHandler = new InternationalJourneysDataHandler(
            $this->partial,
            $this->internationalJourneysIsValidHandler
        );
    }

    public function testSetDataWrongDataValues()
    {
        $this->internationalJourneysIsValidHandler->shouldReceive('isValid')
            ->andReturn(true);

        $this->internationalJourneysDataHandler->setData($this->qaForm);
    }

    public function testSetDataModifyForm()
    {
        $this->internationalJourneysIsValidHandler->shouldReceive('isValid')
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
            'priority' => 20
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

        $this->internationalJourneysDataHandler->setData($this->qaForm);
    }
}
