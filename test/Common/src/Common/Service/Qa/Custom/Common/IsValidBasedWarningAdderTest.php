<?php

namespace CommonTest\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\IsValidHandlerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Helper\Partial;
use Zend\Form\Element\Hidden;

/**
 * IsValidBasedWarningAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IsValidBasedWarningAdderTest extends MockeryTestCase
{
    const WARNING_KEY = 'warning.key';

    const PRIORITY = 25;

    private $qaForm;

    private $partial;

    private $isValidHandler;

    private $isValidBasedWarningAdder;

    public function setUp(): void
    {
        $this->qaForm = m::mock(QaForm::class);

        $this->partial = m::mock(Partial::class);

        $this->isValidHandler = m::mock(IsValidHandlerInterface::class);

        $this->isValidBasedWarningAdder = new IsValidBasedWarningAdder(
            $this->partial,
            $this->isValidHandler
        );
    }

    public function testSetDataWrongDataValues()
    {
        $this->isValidHandler->shouldReceive('isValid')
            ->andReturn(true);

        $this->isValidBasedWarningAdder->add($this->isValidHandler, $this->qaForm, self::WARNING_KEY, self::PRIORITY);
    }

    public function testSetDataModifyForm()
    {
        $this->isValidHandler->shouldReceive('isValid')
            ->andReturn(false);

        $warningMarkup = '<h1>warning markup</h1>';

        $this->partial->shouldReceive('__invoke')
            ->with(
                'partials/warning-component',
                ['translationKey' => self::WARNING_KEY]
            )
            ->once()
            ->andReturn($warningMarkup);

        $warningElementParams = [
            'name' => 'warning',
            'type' => Html::class,
            'attributes' => [
                'value' => $warningMarkup
            ]
        ];

        $warningFlagsParams = [
            'priority' => self::PRIORITY
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
            ->with($warningElementParams, $warningFlagsParams)
            ->once();

        $this->qaForm->shouldReceive('getQuestionFieldset')
            ->andReturn($questionFieldset);

        $this->isValidBasedWarningAdder->add($this->isValidHandler, $this->qaForm, self::WARNING_KEY, self::PRIORITY);
    }
}
