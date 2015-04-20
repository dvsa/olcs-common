<?php

/**
 * FormCollection Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\Stdlib\PriorityQueue;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\View\Helper;
use Common\Form\View\Helper\FormCollection as FormCollectionViewHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Collection;
use Zend\Form\Form;
use Common\Form\Elements\Types\PostcodeSearch;
use Common\Form\Elements\Types\FileUploadList;
use Common\Form\Elements\Types\FileUploadListItem;
use Common\Form\Elements\Types\HoursPerWeek;
use Zend\I18n\View\Helper\Translate;
use Zend\I18n\Translator\Translator;

/**
 * FormCollection Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormCollectionTest extends MockeryTestCase
{
    protected $element;

    private function prepareElement($targetElement = 'Text')
    {
        $this->element = new Collection('test');
        $this->element->setOptions(
            array(
                'count' => 1,
                'target_element' => [
                    'type' => $targetElement
                ],
                'should_create_template' => true,
                'hint' => 'Hint',
                'label' => 'Label',
            )
        );
        $this->element->setAttribute('class', 'class');
        $this->element->prepareElement(new Form());
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithNoRendererPlugin()
    {
        $this->prepareElement();
        $view = new JsonRenderer();

        $viewHelper = new FormCollectionViewHelper();
        $viewHelper->setView($view);
        $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputString('');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithHintAtBottom()
    {
        $this->prepareElement();

        $viewHelper = $this->prepareViewHelper();

        $this->element->setOption('hint_at_bottom', true);

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<fieldset class="class" data-group="test"><legend>(.*)<\/legend>'
            . '<span data-template="(.*)"><\/span><p class="hint">(.*)<\/p><\/fieldset>$/'
        );
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
            '/^<fieldset class="class" data-group="test"><legend>(.*)<\/legend><p class="hint">(.*)<\/p>'
            . '<span data-template="(.*)"><\/span><\/fieldset>$/'
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
            '/^<fieldset class="class" data-group="test"><legend>(.*)<\/legend><p class="hint">(.*)<\/p>'
            . '<fieldset data-group="(.*)"><\/fieldset><span data-template="(.*)"><\/span><\/fieldset>/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForPostCodeElement()
    {
        $this->element = new PostcodeSearch('postcode');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<fieldset data-group="postcode"><\/fieldset>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForPostCodeElementWithMessages()
    {
        $this->element = new PostcodeSearch('postcode');
        $this->element->setMessages(['Message']);

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><ul><li>(.*)<\/li><\/ul>'
            . '<fieldset data-group="postcode"><\/fieldset><\/div>$/'
        );
    }

    private function prepareViewHelper()
    {
        $translator = new Translator();
        $translateHelper = new Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('formRow', new Helper\FormRow());
        $helpers->setService('translate', $translateHelper);
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new FormCollectionViewHelper();
        $viewHelper->setView($view);
        $viewHelper->setTranslator($translator);

        return $viewHelper;
    }

    public function testReadOnly()
    {
        $mockElement = m::mock('Zend\Form\ElementInterface');
        $mockElement->shouldReceive('getOption')->with('hint')->andReturnNull();
        $mockElement->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        $mockHelper = m::mock('Common\Form\View\Helper\Readonly\FormRow');
        $mockHelper->shouldReceive('__invoke')->with($mockElement)->andReturn('element');

        $iterator = new PriorityQueue();
        $iterator->insert($mockElement);

        $mockFieldset = m::mock('Zend\Form\FieldsetInterface');
        $mockFieldset->shouldReceive('getMessages')->andReturn([]);
        $mockFieldset->shouldReceive('getAttributes')->andReturn([]);
        $mockFieldset->shouldReceive('getIterator')->andReturn($iterator);
        $mockFieldset->shouldReceive('getOption')->with('readonly')->andReturn(true);
        $mockFieldset->shouldReceive('getOption')->with('hint')->andReturnNull();
        $mockFieldset->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        $mockView = m::mock('Zend\View\Renderer\PhpRenderer');
        $mockView->shouldReceive('formCollection')->andReturn($mockHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformrow')->andReturn($mockHelper);

        $sut = new FormCollectionViewHelper();
        $sut->setView($mockView);

        $markup = $sut->__invoke($mockFieldset);
        $this->assertEquals('<ul class="definition-list">element</ul>', $markup);
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForFileUploadListElement()
    {
        $this->element = new FileUploadList('files');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<ul data-group="files"><\/ul>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForFileUploadListItemElement()
    {
        $this->element = new FileUploadListItem('files');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex('/^<li data-group="files"><\/li>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForHoursWithMessages()
    {
        $this->element = new HoursPerWeek('hpw');
        $this->element->setMessages(
            [
                'hoursPerWeekContent' => [
                    'field' => [
                        'MESSAGE'
                    ]
                ]
            ]
        );

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection', '/');

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><ul><li>(.*)<\/li><\/ul>'
            . '<fieldset data-group="hpw"><\/fieldset><\/div>$/'
        );
    }
}
