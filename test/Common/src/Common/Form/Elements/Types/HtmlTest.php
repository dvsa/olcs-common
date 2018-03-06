<?php

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Types\Html;
use Common\Form\Form;
use Common\Form\View\Helper\FormElement;
use Mockery;
use PHPUnit_Framework_TestCase;
use Zend\View\Renderer\PhpRenderer;

class HtmlTest extends PHPUnit_Framework_TestCase
{
    const INITIAL_HTML_PAYLOAD = '<em>TEST</em>';
    const UPDATED_HTML_PAYLOAD = '<em>TEST 2</em>';
    const MALICIOUS_HTML_PAYLOAD = '<script>alert("TEST")</script>';

    /** @var Html */
    private $htmlElement;

    /** @var Form */
    private $form;

    /** @var FormElement */
    private $helper;

    public function setUp()
    {
        $this->helper = new FormElement();
        /** @var PhpRenderer|Mockery\MockInterface $mockRenderer */
        $mockRenderer = Mockery::mock(PhpRenderer::class);
        $this->helper->setView($mockRenderer);

        $this->htmlElement = new Html('html');
        $this->htmlElement->setAttribute('value', self::INITIAL_HTML_PAYLOAD);

        $this->form = new Form();
        $this->form->add($this->htmlElement);
    }

    public function testThatHtmlIsRendered()
    {
        $this->assertSame(self::INITIAL_HTML_PAYLOAD, $this->render());
    }

    public function testThatHtmlIsRenderedWhenSetByAttribute()
    {
        $this->htmlElement->setAttribute('value', self::UPDATED_HTML_PAYLOAD);
        $this->assertSame(self::UPDATED_HTML_PAYLOAD, $this->render());
    }

    public function testThatHtmlIsRenderedEscapedWhenSetByValue()
    {
        $this->htmlElement->setValue(self::UPDATED_HTML_PAYLOAD);
        $this->assertSame(self::UPDATED_HTML_PAYLOAD, $this->render());
    }

    public function testThatHtmlCannotBeInjectedViaSetData()
    {
        $this->form->setData(['html' => self::MALICIOUS_HTML_PAYLOAD]);
        $this->assertSame(self::INITIAL_HTML_PAYLOAD, $this->render());
    }

    public function testThatHtmlCannotBeInjectedViaPopulateValues()
    {
        $this->form->setData(['html' => self::MALICIOUS_HTML_PAYLOAD]);
        $this->assertSame(self::INITIAL_HTML_PAYLOAD, $this->render());
    }

    private function render()
    {
        return $this->helper->render($this->htmlElement);
    }
}
