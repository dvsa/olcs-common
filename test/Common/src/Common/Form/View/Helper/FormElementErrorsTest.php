<?php

/**
 * FormElementErrors Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;

/**
 * FormElementErrors Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormElementErrorsTest extends \PHPUnit\Framework\TestCase
{
    protected $element;

    public function setUp(): void
    {
        $this->element = new \Laminas\Form\Element\Text('test');
        $this->element->setMessages(['Message']);
    }

    /**
     * @outputBuffering disabled
     */
    public function testRender()
    {
        $translator = new \Laminas\I18n\Translator\Translator();
        $translateHelper = new \Laminas\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);

        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormElementErrors();
        $viewHelper->setView($view);
        $markup = $viewHelper($this->element);

        $this->assertSame('<p class="error__text">Message</p>', $markup);
    }
}
