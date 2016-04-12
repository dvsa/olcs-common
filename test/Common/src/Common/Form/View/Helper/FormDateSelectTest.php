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
use Zend\Form\Element\DateSelect;
use Zend\Form\Element\Text;
use Zend\Form\View\Helper\FormInput;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Service\ViewHelperManagerFactory;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * Form Date Select Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormDateSelectTest extends MockeryTestCase
{
    private $sut;

    public function setUp()
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
            . '<input type="select" name="day" id="_day" pattern="&#x5C;d&#x2A;" maxlength="2" value="">'
        . '</div> '
        . '<div class="field inline-text">'
            . '<label for="_month">translated-date-Month</label>'
            . '<input type="select" name="month" id="_month" pattern="&#x5C;d&#x2A;" maxlength="2" value="">'
        . '</div> '
        . '<div class="field inline-text">'
            . '<label for="_year">translated-date-Year</label>'
            . '<input type="select" name="year" id="_year" pattern="&#x5C;d&#x2A;" maxlength="4" value="">'
        . '</div>';

        $this->assertEquals($expected, $markup);
    }

    public function testRenderWrongElement()
    {
        $this->setExpectedException(\Zend\Form\Exception\InvalidArgumentException::class);

        $element = new Text('date');

        $this->sut->render($element);
    }

    public function testRenderElementWithNoName()
    {
        $this->setExpectedException(\Zend\Form\Exception\DomainException::class);

        $element = new DateSelect(null);

        $this->sut->render($element);
    }
}
