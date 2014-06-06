<?php

/**
 * FormCollection Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\View\Helper;

/**
 * FormCollection Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $element;

    private function prepareElement($targetElement = 'Text')
    {
        $this->element = new \Zend\Form\Element\Collection('test');
        $this->element->setOptions([
            'count' => 1,
            'target_element' => [
                'type' => $targetElement
            ],
            'should_create_template' => true,
            'hint' => 'Hint',
            'label' => 'Label',
        ]);
        $this->element->setAttribute('class', 'class');
        $this->element->prepareElement(new \Zend\Form\Form());
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithNoRendererPlugin()
    {
        $this->prepareElement();
        $view = new JsonRenderer();

        $viewHelper = new \Common\Form\View\Helper\FormCollection();
        $viewHelper->setView($view);
        $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputString('');
    }


    /**
     * @outputBuffering disabled
     */
    public function testRender()
    {
        $this->prepareElement();

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<fieldset class="class"><legend>(.*)<\/legend><p class="hint">(.*)<\/p><span data-template="(.*)">'
            . '<\/span><\/fieldset>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithFieldsetAsTargetElement()
    {
        $this->prepareElement('Fieldset');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<fieldset class="class"><legend>(.*)<\/legend><p class="hint">(.*)<\/p><fieldset><\/fieldset>'
            . '<span data-template="(.*)"><\/span><\/fieldset>/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForPostCodeElement()
    {
        $this->element = new \Common\Form\Elements\Types\PostcodeSearch('postcode');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<fieldset><\/fieldset>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForPostCodeElementWithMessages()
    {
        $this->element = new \Common\Form\Elements\Types\PostcodeSearch('postcode');
        $this->element->setMessages(['Message']);

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><ul><li>(.*)<\/li><\/ul><fieldset><\/fieldset><\/div>$/'
        );
    }

    private function prepareViewHelper()
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $translateHelper = new \Zend\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('formRow', new Helper\FormRow());
        $helpers->setService('translate', $translateHelper);
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormCollection();
        $viewHelper->setView($view);
        $viewHelper->setTranslator($translator);

        return $viewHelper;
    }
}
