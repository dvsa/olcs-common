<?php

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Types\PlainText;
use Common\Form\Form;
use Common\Form\View\Helper\FormElement;
use Common\Form\View\Helper\FormPlainText;
use Mockery;
use Zend\View\Renderer\PhpRenderer;

class PlainTextTest extends \PHPUnit\Framework\TestCase
{
    const INITIAL_TEXT_PAYLOAD = 'TEST';
    const UPDATED_TEXT_PAYLOAD = 'TEST 2';
    const MALICIOUS_HTML_PAYLOAD = '<script>alert("TEST")</script>';

    /** @var PlainText */
    private $plainTextElement;

    /** @var Form */
    private $form;

    /** @var FormElement */
    private $helper;

    public function setUp()
    {
        $formPlainText = new FormPlainText();

        /** @var PhpRenderer|Mockery\MockInterface $mockRenderer */
        $mockRenderer = Mockery::mock(PhpRenderer::class);
        $mockRenderer->shouldReceive('plugin')->with('form_plain_text')->andReturn($formPlainText);
        $mockRenderer->shouldReceive('translate')->andReturnUsing(
            function ($arg) {
                return $arg;
            }
        );
        $formPlainText->setView($mockRenderer);


        $this->helper = new FormElement();
        $this->helper->setView($mockRenderer);

        $this->plainTextElement = new PlainText('text');
        $this->plainTextElement->setAttribute('value', self::INITIAL_TEXT_PAYLOAD);

        $this->form = new Form();
        $this->form->add($this->plainTextElement);
    }

    public function testThatTextIsRendered()
    {
        $this->assertSame(self::INITIAL_TEXT_PAYLOAD, $this->render());
    }

    public function testThatTextIsRenderedWhenSetByAttribute()
    {
        $this->plainTextElement->setAttribute('value', self::UPDATED_TEXT_PAYLOAD);
        $this->assertSame(self::UPDATED_TEXT_PAYLOAD, $this->render());
    }

    public function testThatTextIsRenderedEscapedWhenSetByValue()
    {
        $this->plainTextElement->setValue(self::UPDATED_TEXT_PAYLOAD);
        $this->assertSame(self::UPDATED_TEXT_PAYLOAD, $this->render());
    }

    public function testThatTextCannotBeInjectedViaSetData()
    {
        $this->form->setData(['text' => self::MALICIOUS_HTML_PAYLOAD]);
        $this->assertSame(self::INITIAL_TEXT_PAYLOAD, $this->render());
    }

    public function testThatTextCannotBeInjectedViaPopulateValues()
    {
        $this->form->populateValues(['text' => self::MALICIOUS_HTML_PAYLOAD]);
        $this->assertSame(self::INITIAL_TEXT_PAYLOAD, $this->render());
    }

    private function render()
    {
        return $this->helper->render($this->plainTextElement);
    }
}
