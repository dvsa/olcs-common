<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\Custom\EcmtShortTerm\NiWarningConditionalAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\View\Helper\Partial;

/**
 * NiWarningConditionalAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NiWarningConditionalAdderTest extends MockeryTestCase
{
    private $form;

    private $fieldset;

    private $partial;

    private $niWarningConditionalAdder;

    public function setUp(): void
    {
        $this->form = m::mock(Form::class);

        $this->fieldset = m::mock(Fieldset::class);

        $this->partial = m::mock(Partial::class);

        $this->niWarningConditionalAdder = new NiWarningConditionalAdder($this->partial);
    }

    public function testAddWhenShowNiWarningTrue()
    {
        $niWarningMarkup = '<h1>ni warning markup</h1>';

        $this->partial->shouldReceive('__invoke')
            ->with(
                'partials/warning-component',
                ['translationKey' => 'permits.page.number-of-trips.northern-ireland.warning']
            )
            ->once()
            ->andReturn($niWarningMarkup);

        $expectedNiWarningParameters = [
            'name' => 'niWarning',
            'type' => Html::class,
            'attributes' => [
                'value' => $niWarningMarkup
            ]
        ];

        $this->fieldset->shouldReceive('add')
            ->with($expectedNiWarningParameters)
            ->once();

        $this->niWarningConditionalAdder->addIfRequired($this->fieldset, true);
    }

    public function testDoNothinWhenNiWarningFalse()
    {
        $this->fieldset->shouldReceive('add')
            ->never();

        $this->niWarningConditionalAdder->addIfRequired($this->fieldset, false);
    }
}
