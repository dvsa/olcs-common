<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Types\FileUploadList;
use Common\Form\Elements\Types\FileUploadListItem;
use Common\Form\Elements\Types\HoursPerWeek;
use Common\Form\Elements\Types\PostcodeSearch;
use Common\Form\Elements\Types\RadioHorizontal;
use Common\Form\Elements\Types\RadioVertical;
use Common\Form\View\Helper\FormCollection as FormCollectionViewHelper;
use Common\Form\View\Helper\FormCollection;
use Common\Form\View\Helper\Readonly\FormFieldset;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\View\Helper;
use Zend\I18n\Translator\Translator;
use Zend\I18n\View\Helper\Translate;
use Zend\Stdlib\PriorityQueue;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\Renderer\PhpRenderer;

/**
 * @covers \Common\Form\View\Helper\FormCollection
 */
class FormCollectionTest extends MockeryTestCase
{
    /** @var  \Zend\Form\FieldsetInterface | \Zend\Form\ElementInterface */
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
        $viewHelper($this->element, 'formCollection');

        $this->expectOutputString('');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithHintAtBottom()
    {
        $this->prepareElement();

        $viewHelper = $this->prepareViewHelper();

        $this->element->setOption('hint-position', 'below');

        echo $viewHelper($this->element, 'formCollection');

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

        echo $viewHelper($this->element, 'formCollection');

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

        echo $viewHelper($this->element, 'formCollection');

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

        echo $viewHelper($this->element, 'formCollection');

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

        echo $viewHelper($this->element, 'formCollection');

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><ul><li>(.*)<\/li><\/ul>'
            . '<fieldset data-group="postcode"><\/fieldset><\/div>$/'
        );
    }

    /**
     * Prepare View Helper
     *
     * @return FormCollectionViewHelper
     */
    private function prepareViewHelper()
    {
        $translator = new Translator();
        $translateHelper = new Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('formRow', new Helper\FormRow());
        $helpers->setService('form_element_errors', new Helper\FormElementErrors());
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

        $mockFieldsetHlpr = m::mock(\Common\Form\View\Helper\Readonly\FormFieldset::class)->makePartial();

        $iterator = new PriorityQueue();
        $iterator->insert($mockElement);

        $mockFieldset = m::mock('Zend\Form\FieldsetInterface');
        $mockFieldset->shouldReceive('getMessages')->andReturn([]);
        $mockFieldset->shouldReceive('getAttribute')->with('class')->andReturn('unit_CssClass');
        $mockFieldset->shouldReceive('getAttributes')->andReturn([]);
        $mockFieldset->shouldReceive('getIterator')->andReturn($iterator);
        $mockFieldset->shouldReceive('getOption')->with('readonly')->andReturn(true);
        $mockFieldset->shouldReceive('getOption')->with('hint')->andReturnNull();
        $mockFieldset->shouldReceive('getOption')->with('hintClass')->andReturnNull()->once();
        $mockFieldset->shouldReceive('getOption')->with('hintFormat')->andReturnNull();
        $mockFieldset->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        /** @var \Zend\View\Renderer\PhpRenderer | m\MockInterface $mockView */
        $mockView = m::mock(\Zend\View\Renderer\PhpRenderer::class);
        $mockView->shouldReceive('formCollection')->andReturn($mockHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformrow')->andReturn($mockHelper);
        $mockView->shouldReceive('plugin')->with(FormFieldset::class)->andReturn($mockFieldsetHlpr);

        $sut = new FormCollectionViewHelper();
        $sut->setView($mockView);

        $markup = $sut($mockFieldset);  //  invoke
        $this->assertEquals('<ul class="definition-list readonly">element</ul>', $markup);
    }

    public function testRenderForFileUploadListElementNotItems()
    {
        $viewHelper = $this->prepareViewHelper();

        static::assertEquals('', $viewHelper(new FileUploadList('files'), 'formCollection'));
    }

    public function testRenderForFileUploadListElement()
    {
        $this->element = new FileUploadList('files');
        $this->element
            ->setAttributes(
                [
                    'title' => 'unit_attr1',
                ]
            )
            ->setOptions(
                [
                    'hint' => '@unit_hint@',
                ]
            );

        $this->element->add(new FileUploadListItem('file1'));

        $viewHelper = $this->prepareViewHelper();

        /** @var \Zend\View\Renderer\PhpRenderer | m\MockInterface $mockView */
        $mockView = m::mock($viewHelper->getView())->makePartial();
        $mockView
            ->shouldReceive('translate')->andReturnUsing(
                function ($arg) {
                    return '_TRANSL_' . $arg;
                }
            );
        $viewHelper->setView($mockView);

        $actual = $viewHelper($this->element, 'formCollection');

        static::assertEquals(
            '<div class="help__text">' .
            '<h3 class="file__heading">_TRANSL_common.file-upload.table.col.FileName</h3>' .
            '<ul title="unit_attr1" data-group="files">' .
            '<p class="hint">_TRANSL_@unit_hint@</p>'.
            '<li data-group="file1"></li>'.
            '</ul>' .
            '</div>',
            $actual
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderForFileUploadListItemElement()
    {
        $this->element = new FileUploadListItem('files');

        $viewHelper = $this->prepareViewHelper();

        echo $viewHelper($this->element, 'formCollection');

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

        echo $viewHelper($this->element, 'formCollection');

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><ul><li>(.*)<\/li><\/ul>'
            . '<fieldset data-group="hpw"><\/fieldset><\/div>$/'
        );
    }

    public function testRenderRadioHorizontal()
    {
        $mockView = m::mock(PhpRenderer::class);
        $mockView->shouldReceive('plugin')->with('formrow')->once()->andReturn(
            m::mock(Helper\AbstractHelper::class)->shouldReceive('render')->getMock()
        );
        $mockView->shouldReceive('plugin')->with('escapehtml')->once()->andReturn(
            m::mock(Helper\AbstractHelper::class)->shouldReceive('render')->getMock()
        );
        $mockView->shouldReceive('plugin')->with('escapehtmlattr')->once()->andReturn(
            m::mock(Helper\AbstractHelper::class)->shouldReceive('render')->getMock()
        );
        $mockView->shouldReceive('plugin')->with('formRadioHorizontal')->once()->andReturn(
            function () {
                return "formRadioHorizontal MARKUP";
            }
        );

        $sut = new FormCollection();
        $sut->setView($mockView);

        $element = new Fieldset();
        $element->add(new RadioHorizontal('test'));

        $output = $sut->render($element);

        $this->assertSame('<fieldset>formRadioHorizontal MARKUP</fieldset>', $output);
    }

    public function testRenderRadioVertical()
    {
        $mockView = m::mock(PhpRenderer::class);
        $mockView->shouldReceive('plugin')->with('formRadioVertical')->once()->andReturn(
            function () {
                return "formRadioVertical MARKUP";
            }
        );

        $sut = new FormCollection();
        $sut->setView($mockView);

        $element = new RadioVertical('test');

        $output = $sut->render($element);

        $this->assertSame('formRadioVertical MARKUP', $output);
    }

    /**
     *
     * @outputBuffering disabled
     */
    public function testRenderWithHintAndClass()
    {
        $this->prepareElement();

        $viewHelper = $this->prepareViewHelper();

        $this->element->setOption('hint-position', 'below');
        $this->element->setOption('hintClass', 'form-hint');

        echo $viewHelper($this->element, 'formCollection');

        $this->expectOutputRegex(
            '/^<fieldset class="class" data-group="test"><legend>(.*)<\/legend>'
            . '<span data-template="(.*)"><\/span><p class="form-hint">(.*)<\/p><\/fieldset>$/'
        );
    }
}
