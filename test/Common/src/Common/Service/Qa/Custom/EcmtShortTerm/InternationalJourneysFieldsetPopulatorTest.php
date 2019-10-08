<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysFieldsetPopulator;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\View\Helper\Partial;

/**
 * InternationalJourneysFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InternationalJourneysFieldsetPopulatorTest extends MockeryTestCase
{
    private $radioOptions;

    private $form;

    private $fieldset;

    private $partial;

    private $radioFieldsetPopulator;

    private $internationalJourneysFieldsetPopulator;

    public function setUp()
    {
        $expectedWarningVisibleParameters = [
            'name' => 'warningVisible',
            'type' => Hidden::class,
            'attributes' => [
                'value' => 0
            ]
        ];

        $this->radioOptions = [
            'radioKey1' => 'radioValue1',
            'radioKey2' => 'radioValue2'
        ];

        $this->form = m::mock(Form::class);

        $this->fieldset = m::mock(Fieldset::class);
        $this->fieldset->shouldReceive('add')
            ->with($expectedWarningVisibleParameters)
            ->once();

        $this->partial = m::mock(Partial::class);

        $this->radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);

        $this->internationalJourneysFieldsetPopulator = new InternationalJourneysFieldsetPopulator(
            $this->radioFieldsetPopulator,
            $this->partial
        );
    }

    public function testPopulateWithNiWarning()
    {
        $options = [
            'showNiWarning' => true,
            'radio' => $this->radioOptions
        ];

        $niWarningMarkup = '<h1>ni warning markup</h1>';

        $this->partial->shouldReceive('__invoke')
            ->with(
                'partials/warning-component',
                ['translationKey' => 'permits.page.number-of-trips.northern-ireland.warning']
            )
            ->once()
            ->andReturn($niWarningMarkup)
            ->globally()
            ->ordered();

        $expectedNiWarningParameters = [
            'name' => 'niWarning',
            'type' => Html::class,
            'attributes' => [
                'value' => $niWarningMarkup
            ]
        ];

        $this->fieldset->shouldReceive('add')
            ->with($expectedNiWarningParameters)
            ->once()
            ->globally()
            ->ordered();

        $this->radioFieldsetPopulator->shouldReceive('populate')
            ->with($this->form, $this->fieldset, $this->radioOptions)
            ->once()
            ->globally()
            ->ordered();

        $this->internationalJourneysFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    public function testPopulateWithoutNiWarning()
    {
        $options = [
            'showNiWarning' => false,
            'radio' => $this->radioOptions
        ];

        $this->radioFieldsetPopulator->shouldReceive('populate')
            ->with($this->form, $this->fieldset, $this->radioOptions)
            ->once();

        $this->internationalJourneysFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }
}
