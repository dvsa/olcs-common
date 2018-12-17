<?php

namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\JsonRenderer;
use Zend\Form\View\Helper;

/**
 * FormElement Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormElementTest extends \PHPUnit\Framework\TestCase
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
            '/^<p class="hint">(.*)<\/p><input type="text" name="(.*)" class="(.*)" id="(.*)" value="(.*)">$/'
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

        $this->expectOutputRegex('/^plain$/');
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

        $this->expectOutputRegex('/^<a href=".*" class="(.*)">(.*)<\/a>$/');
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
    public function testRenderForActionLinkElementWithMaliciousUrl()
    {
        $this->prepareElement('\Common\Form\Elements\InputFilters\ActionLink');
        $maliciousUrl = '<script>alert("url")</script>';
        $this->element->setValue($maliciousUrl);

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<a href="' . preg_quote(
                htmlspecialchars($maliciousUrl, ENT_QUOTES, 'utf-8'),
                '/'
            ) . '" class="class">(.*)<\/a>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForActionLinkElementWithTarget()
    {
        $this->prepareElement(\Common\Form\Elements\InputFilters\ActionLink::class);
        $this->element->setValue('url');
        $this->element->setAttribute('target', '_blank');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<a href="(.*)" class="(.*)" target="_blank">(.*)<\/a>$/');
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
    public function testRenderForTermsBoxElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\TermsBox');
        $this->element->setValue('foo');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div name="test" class="class&#x20;terms--box" id="test">foo<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForTermsBoxElementWithoutClass()
    {
        $this->prepareElement('\Common\Form\Elements\Types\TermsBox');
        $this->element->setAttribute('class', null);
        $this->element->setValue('foo');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div name="test" class="&#x20;terms--box" id="test">foo<\/div>$/');
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
    public function testRenderForHtmlTranslatedElementWithoutValue()
    {
        $this->prepareElement('\Common\Form\Elements\Types\HtmlTranslated');

        $viewHelper = $this->prepareViewHelper([]);

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertEmpty($markup);
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

        /** @var \Zend\View\Renderer\PhpRenderer | \PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createPartialMock(\Zend\View\Renderer\PhpRenderer::class, array('url'));
        $view->expects($this->any())
            ->method('url')
            ->will($this->returnValue('url'));

        $plainTextService = new \Common\Form\View\Helper\FormPlainText();
        $plainTextService->setTranslator($translator);
        $plainTextService->setView($view);

        $helpers = new HelperPluginManager();
        $helpers->setService('form_text', new Helper\FormText());
        $helpers->setService('form_input', new Helper\FormInput());
        $helpers->setService('form_file', new Helper\FormFile());
        $helpers->setService('translate', $translateHelper);
        $helpers->setService('form_plain_text', $plainTextService);
        $helpers->setService('form', new Helper\Form());

        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormElement();
        $viewHelper->setView($view);

        return $viewHelper;
    }

    public function testRenderForTrafficAreaSet()
    {
        $this->prepareElement(\Common\Form\Elements\Types\TrafficAreaSet::class);

        $this->element
            ->setValue('<ABC>')
            ->setOption('hint-position', 'below');

        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertEquals(
            '<div class="label">&lt;ABC&gt;</div><div class="hint">Hint</div>',
            $markup
        );
    }

    public function testRenderForTrafficAreaSetWithoutHint()
    {
        $this->prepareElement('\\Common\Form\Elements\Types\TrafficAreaSet');

        $this->element->setValue('ABC');
        $this->element->setOption('hint', null);

        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertEquals(
            '<div class="label">ABC</div>',
            $markup
        );
    }

    public function testRenderForTrafficAreaSetWithSuffix()
    {
        $this->prepareElement('\\Common\Form\Elements\Types\TrafficAreaSet');

        $this->element->setValue('ABC');
        $this->element->setOption('hint-suffix', '-foo');

        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertEquals(
            '<p class="hint">Hint</p><div class="label">ABC</div>',
            $markup
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForGuidanceTranslatedElement()
    {
        $this->prepareElement('\Common\Form\Elements\Types\GuidanceTranslated');
        $this->element->setValue('some-translation-key');

        $translations = ['some-translation-key' => 'actual translated string'];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div class="article">actual translated string<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForGuidanceTranslatedElementWithoutValue()
    {
        $this->prepareElement('\Common\Form\Elements\Types\GuidanceTranslated');

        $viewHelper = $this->prepareViewHelper([]);

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertEmpty($markup);
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForGuidanceTranslatedElementWithTokens()
    {
        $this->prepareElement('\Common\Form\Elements\Types\GuidanceTranslated');
        $this->element->setValue('<div>%s and then %s</div>');
        $this->element->setTokens(['foo-key', 'bar-key']);

        $translations = [
            'foo-key' => 'foo string',
            'bar-key' => 'bar string'
        ];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div class="article"><div>foo string and then bar string<\/div><\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForGuidanceTranslatedElementWithTokensViaOptions()
    {
        $this->prepareElement('\Common\Form\Elements\Types\GuidanceTranslated');
        $this->element->setValue('<div>%s and then %s</div>');
        $this->element->setOptions(['tokens' => ['foo-key', 'bar-key']]);

        $translations = [
            'foo-key' => 'foo string',
            'bar-key' => 'bar string'
        ];
        $viewHelper = $this->prepareViewHelper($translations);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<div class="article"><div>foo string and then bar string<\/div><\/div>$/');
    }

    public function testRenderForAttachFilesButton()
    {
        $this->prepareElement('\\Common\Form\Elements\Types\AttachFilesButton');

        $this->element->setValue('My Button');

        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $expected = '<ul class="attach-action__list"><li class="attach-action">'
            . '<label class="attach-action__label"> '
            . '<input type="file" name="test" class="class&#x20;attach-action__input" id="test">'
            . '</label>'
            . '<p class="attach-action__hint">Hint</p></li></ul>';

        $this->assertEquals(
            $expected,
            $markup
        );
    }

    public function testRenderForAttachFilesButtonWithNoClass()
    {
        $this->prepareElement('\\Common\Form\Elements\Types\AttachFilesButton');

        $this->element->setValue('My Button');
        $this->element->setAttribute('class', null);

        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $expected = '<ul class="attach-action__list"><li class="attach-action">'
            . '<label class="attach-action__label"> '
            . '<input type="file" name="test" class="&#x20;attach-action__input" id="test">'
            . '</label>'
            . '<p class="attach-action__hint">Hint</p></li></ul>';

        $this->assertEquals(
            $expected,
            $markup
        );
    }

    public function testRenderHintBelow()
    {
        $this->prepareElement('Text', ['hint-below' => 'HINT BELOW']);

        $viewHelper = $this->prepareViewHelper();

        $output = $viewHelper($this->element, 'formCollection', '/');

        $this->assertSame(
            '<p class="hint">Hint</p><input type="text" name="test" class="class" id="test" value="">'
            .'<div class="hint">HINT BELOW</div>',
            $output
        );
    }

    public function testRenderElementWithError()
    {
        $this->prepareElement();
        $this->element->setMessages(['Message 1']);
        $viewHelper = $this->prepareViewHelper();

        $markup = $viewHelper($this->element, 'formCollection', '/');

        $this->assertSame(
            '<p class="hint">Hint</p><input type="text" name="test" class="class&#x20;error__input" id="test" value="">',
            $markup
        );
    }
}
