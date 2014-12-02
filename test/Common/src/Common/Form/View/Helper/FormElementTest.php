<?php

/**
 * FormElement Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\JsonRenderer;
use Zend\Form\View\Helper;

/**
 * FormElement Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormElementTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\Form\Element
     */
    protected $element;

    private function prepareElement($type = 'Text', $options = array())
    {
        if (strpos($type, '\\') === false) {
            $type = '\Zend\Form\Element\\' . ucfirst($type);
        }

        $options = array_merge(
            array(
                'type' => $type,
                'label' => 'Label',
                'hint' => 'Hint',
            ),
            $options
        );

        $this->element = new $type('test');
        $this->element->setOptions($options);
        $this->element->setAttribute('class', 'class');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithNoRendererPlugin()
    {
        $this->prepareElement();
        $view = new JsonRenderer();

        $viewHelper = new \Common\Form\View\Helper\FormElement();
        $viewHelper->setView($view);
        $viewHelper($this->element, 'formElement', '/');

        $this->expectOutputString('');
    }


    /**
     * @outputBuffering disabled
     */
    public function testRenderForTextElement()
    {
        $this->prepareElement();

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<input type="text" name="(.*)" class="(.*)" value="(.*)"> \\r\\n <p class="hint">(.*)<\/p>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForPlainTextElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\PlainText');

        $viewHelper = $this->prepareViewHelper();
        $this->element->setValue('plain');

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<p class="hint">(.*)<\/p>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForActionLinkElementWithRoute()
    {
        $options = ['route' => 'route'];
        $this->prepareElement('\Common\Form\Elements\InputFilters\ActionLink', $options);

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<a href="(.*)" class="(.*)">(.*)<\/a>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForActionLinkElementWithUrl()
    {
        $this->prepareElement('\Common\Form\Elements\InputFilters\ActionLink');
        $this->element->setValue('url');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<a href="(.*)" class="(.*)">(.*)<\/a>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForHtmlElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\Html');
        $this->element->setValue('<div></div>');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div><\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForHtmlTranslatedElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\HtmlTranslated');
        $this->element->setValue('some-translation-key');

        $translations = ['some-translation-key' => 'actual translated string'];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^actual translated string$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForHtmlTranslatedElementWithTokens()
    {
        $this->prepareElement('\Common\Form\Elements\Types\HtmlTranslated');
        $this->element->setValue('<div>%s and then %s</div>');
        $this->element->setTokens(['foo-key', 'bar-key']);

        $translations = [
            'foo-key' => 'foo string',
            'bar-key' => 'bar string'
        ];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div>foo string and then bar string<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForHtmlTranslatedElementWithTokensViaOptions()
    {
        $this->prepareElement('\Common\Form\Elements\Types\HtmlTranslated');
        $this->element->setValue('<div>%s and then %s</div>');
        $this->element->setOptions(['tokens' => ['foo-key', 'bar-key']]);

        $translations = [
            'foo-key' => 'foo string',
            'bar-key' => 'bar string'
        ];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div>foo string and then bar string<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForTableElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\Table');

        $mockTable = $this->getMockBuilder('\Common\Service\Table\TableBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();

        $mockTable->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<table></table>'));

        $this->element->setTable($mockTable);

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<table><\/table>$/');
    }

    private function prepareViewHelper($translateMap = null)
    {
        $translator = new \CommonTest\Util\DummyTranslator();
        if (!is_null($translateMap)) {
            $translator->setMap($translateMap);
        }

        $translateHelper = new \Zend\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $view = $this->getMock('\Zend\View\Renderer\PhpRenderer', array('url'));
        $view->expects($this->any())
            ->method('url')
            ->will($this->returnValue('url'));

        $plainTextService = new \Common\Form\View\Helper\FormPlainText();
        $plainTextService->setTranslator($translator);
        $plainTextService->setView($view);

        $helpers = new HelperPluginManager();
        $helpers->setService('form_text', new Helper\FormText());
        $helpers->setService('form_input', new Helper\FormInput());
        $helpers->setService('translate', $translateHelper);
        $helpers->setService('form_plain_text', $plainTextService);

        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormElement();
        $viewHelper->setView($view);

        return $viewHelper;
    }
}
