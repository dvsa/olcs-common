<?php

/**
 * Form Date Select Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormDateSelect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Text;
use Laminas\Form\View\Helper\FormInput;
use Laminas\I18n\Translator\Translator;
use Laminas\Mvc\Service\ViewHelperManagerFactory;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Form Date Select Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormDateSelectTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $formInput = m::mock(FormInput::class)->makePartial();

        $translator = m::mock(Translator::class);
        $translator->shouldReceive('translate')->andReturnUsing(
            function ($key) {
                return 'translated-' . $key;
            }
        );

        $helpers = new HelperPluginManager();
        $helpers->setService('forminput', $formInput);

        /** @var PhpRenderer $view */
        $view = m::mock(PhpRenderer::class)->makePartial();
        $view->setHelperPluginManager($helpers);

        $this->sut = new FormDateSelect();
        $this->sut->setView($view);
        $this->sut->setTranslator($translator);
    }

    public function testRender()
    {
        $element = new DateSelect('date');

        $markup = $this->sut->render($element);

        $expected = '<div class="field inline-text">'
            . '<label for="_day">translated-date-Day</label>'
            . '<input type="select" name="day" id="_day" pattern="&#x5C;d&#x2A;" maxlength="2" class="govuk-input&#x20;govuk-date-input__input&#x20;govuk-input--width-2" value="">'
        . '</div> '
        . '<div class="field inline-text">'
            . '<label for="_month">translated-date-Month</label>'
            . '<input type="select" name="month" id="_month" pattern="&#x5C;d&#x2A;" maxlength="2" class="govuk-input&#x20;govuk-date-input__input&#x20;govuk-input--width-2" value="">'
        . '</div> '
        . '<div class="field inline-text">'
            . '<label for="_year">translated-date-Year</label>'
            . '<input type="select" name="year" id="_year" pattern="&#x5C;d&#x2A;" maxlength="4" class="govuk-input&#x20;govuk-date-input__input&#x20;govuk-input--width-4" value="">'
        . '</div>';

        $this->assertEquals($expected, $markup);
    }

    public function testRenderWrongElement()
    {
        $this->expectException(\Laminas\Form\Exception\InvalidArgumentException::class);

        $element = new Text('date');

        $this->sut->render($element);
    }

    public function testRenderElementWithNoName()
    {
        $this->expectException(\Laminas\Form\Exception\DomainException::class);

        $element = new DateSelect(null);

        $this->sut->render($element);
    }
}
