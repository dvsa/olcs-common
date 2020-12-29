<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Types\AttachFilesButton;
use Laminas\Form\Element\DateSelect;
use Laminas\View\HelperPluginManager;
use Laminas\Form\View\Helper as LaminasHelper;
use Common\Form\View\Helper as CommonHelper;

/**
 * @covers \Common\Form\View\Helper\FormRow
 * @covers \Common\Form\View\Helper\Extended\FormRow
 */
class FormRowTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Prepare element for test
     *
     * @param string $type    Element type
     * @param array  $options Options for element
     *
     * @return \Laminas\Form\Element
     */
    private function prepareElement(
        $type = 'Text',
        $options = array(),
        $attributes = array('class' => 'class')
    ) {
        if (strpos($type, '\\') === false) {
            $type = '\Laminas\Form\Element\\' . ucfirst($type);
        }

        $options = array_merge(
            array(
                'type'  => $type,
                'label' => 'Label',
                'hint'  => 'Hint',
            ),
            $options
        );

        $element = new $type('test');
        $element->setOptions($options);
        $element->setAttributes($attributes);

        return $element;
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderClassic()
    {
        $element = $this->prepareElement();
        $element->setMessages(['Message']);
        $element->setLabelOption('always_wrap', true);

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><div class="field ">' .
            '<p class="error__text">Message<\/p><label>(.*)<\/label>(.*)<\/div><\/div>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderClassicNoLabel()
    {
        $element = $this->prepareElement('Text', ['label' => null]);
        $element->setMessages(['Message']);

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex(
            '/^<div class="validation-wrapper"><div class="field ">' .
            '<p class="error__text">Message<\/p>(.*)<\/div><\/div>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderClassicWithId()
    {
        $element = $this->prepareElement();
        $element->setAttribute('id', 'test');

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<div class="field "><label(.*)>(.*)<\/label>(.*)<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderClassicWithPartial()
    {
        $element = $this->prepareElement();

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element, null, null, 'partial');

        $this->expectOutputRegex('/^<div class="field "><\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderActionButton()
    {
        $element = $this->prepareElement('Common\Form\Elements\InputFilters\ActionButton');

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderNoRender()
    {
        $element = $this->prepareElement('Common\Form\Elements\InputFilters\NoRender');

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderTable()
    {
        $element = $this->prepareElement('Common\Form\Elements\Types\Table');

        $mockTable = $this->getMockBuilder('\Common\Service\Table\TableBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();

        $mockTable->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<table></table>'));

        $element->setTable($mockTable);

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<div class="field "><table><\/table><\/div>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderSingleCheckbox()
    {
        $element = $this->prepareElement('Common\Form\Elements\InputFilters\SingleCheckbox');
        $element->setLabelOption('always_wrap', true);

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex(
            '/^<div class="field "><div class="govuk-checkboxes__item"><label>(.*)<\/label>(.*)<\/div><\/div>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderCheckbox()
    {
        $element = $this->prepareElement(
            'Common\Form\Elements\InputFilters\Checkbox',
            [
                'label_options' => [
                    'label_position' => 'append',
                ],
            ]
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex(
            '/^<div class="field "><div class="govuk-checkboxes__item"><label for="test">(.*)<\/label>(.*)<\/div><\/div>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderRadioNoAttribute()
    {
        $element = $this->prepareElement('Radio');

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<fieldset><legend>(.*)<\/legend><\/fieldset>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderRadioLegendAttribute()
    {
        $element = $this->prepareElement(
            'Radio',
            [
                "legend-attributes" => [
                    'class' => 'A_CLASS',
                ],
            ]
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<fieldset><legend class="A_CLASS">(.*)<\/legend><\/fieldset>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderRadioWithDataGroupAttribute()
    {
        $element = $this->prepareElement(
            'Radio',
            [
                "fieldset-data-group" => 'data-group',
            ]
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<fieldset data-group="data-group"><legend>(.*)<\/legend><\/fieldset>$/');
    }

    public function testRenderRadioWithInlineAttribute()
    {
        $element = $this->prepareElement(
            'Radio',
            [
                "fieldset-attributes" => [
                    "class"      => "inline",
                    "data-group" => "data-group",
                ],
            ]
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex(
            '/^<fieldset class="inline" data-group="data-group"><legend>(.*)<\/legend><\/fieldset>$/'
        );
    }

    /**
     * @outputBuffering disabled
     * @group formRow
     */
    public function testRenderCsrfElement()
    {
        $element = $this->prepareElement(
            'Csrf',
            [
                'csrf_options' => [
                    'messageTemplates' => [
                        'notSame' => 'csrf-message',
                    ],
                    'timeout'          => 600,
                ],
                'name'         => 'security',
            ],
            ['id' => 'security']
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString('<label for="security">Label</label>');
    }

    /**
     * @outputBuffering disabled
     * @group formRow
     */
    public function testRenderVisuallyHiddenElement()
    {
        $element = $this->prepareElement(
            'Text',
            [
                'name' => 'text',
            ],
            ['class' => 'visually-hidden']
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<div class="field visually-hidden">(.*)<\/div>$/');
    }

    /**
     * @outputBuffering disabled
     * @group formRow
     */
    public function testRenderHiddenElement()
    {
        $element = $this->prepareElement(
            'Hidden',
            [
                'name' => 'hidden',
            ],
            ['class' => 'visually-hidden']
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString('<label for="test">Label</label>');
    }

    public function renderRadioProvider()
    {
        return [
            [null],
            [["class" => ""]],
        ];
    }

    private function prepareHelper()
    {
        $translator = new \Laminas\I18n\Translator\Translator();
        $translateHelper = new \Laminas\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);
        $helpers->setService('form_label', new LaminasHelper\FormLabel());
        $helpers->setService('form_element', new CommonHelper\FormElement());
        $helpers->setService('form_text', new LaminasHelper\FormText());

        $view = $this->createPartialMock(
            'Laminas\View\Renderer\PhpRenderer',
            array('render')
        );

        $view->setHelperPluginManager($helpers);

        // Set the view of element errors then set the service
        $formElementErrors = new CommonHelper\FormElementErrors();
        $formElementErrors->setView($view);
        $helpers->setService('form_element_errors', $formElementErrors);

        $viewHelper = new CommonHelper\FormRow();
        $viewHelper->setView($view);
        $viewHelper->setTranslator($translator);

        return $viewHelper;
    }

    public function testRenderWithRenderAsFieldset()
    {
        $element = $this->prepareElement();
        $element->setOption('render_as_fieldset', true);

        $viewHelper = $this->prepareHelper();
        $markup = $viewHelper($element);

        $this->assertEquals(
            '<fieldset class="fieldset--primary"><legend>Label</legend><p class="hint">Hint</p></fieldset>',
            $markup
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderReadonlyElement()
    {
        $element = $this->prepareElement(
            'Common\Form\Elements\Types\Readonly',
            [
                'name'  => 'readonly',
                'label' => 'Foo',
            ],
            []
        );

        $element->setValue('Bar');

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString('<div class="field read-only "><p>Foo<br><b>Bar</b></p></div>');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderDateSelectElement()
    {
        $element = $this->prepareElement(
            'DateSelect',
            [
                'name'         => 'date',
                'label'        => 'Foo',
                'label-suffix' => 'unit_LabelSfx',
            ],
            []
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString(
            '<div class="field ">' .
            '<fieldset class="date">' .
            '<legend>Foo unit_LabelSfx</legend>' .
            '<p class="hint">Hint</p>' .
            '</fieldset>' .
            '</div>'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderDateSelectWithFieldsetClass()
    {
        $element = $this->prepareElement(
            'DateSelect',
            [
                'name'          => 'date',
                'label'         => 'Foo',
                'fieldsetClass' => 'user',
                'hint'          => null,
            ],
            []
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString(
            '<div class="field "><fieldset class="user"><legend>Foo</legend></fieldset></div>'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderDateTimeSelectElement()
    {
        $element = $this->prepareElement(
            'DateTimeSelect',
            [
                'name'  => 'date',
                'label' => 'Foo',
            ],
            []
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString(
            '<div class="field "><fieldset class="date"><legend>Foo</legend><p class="hint">Hint</p></fieldset></div>'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderAttachFilesButtonElement()
    {
        $element = new AttachFilesButton('files');
        $element->setOptions(
            [
                'type'  => AttachFilesButton::class,
                'label' => 'Label',
                'hint'  => 'Hint',
            ]
        );
        $element->setAttributes(
            ['class' => 'fileUploadTest']
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputString(
            '<div class=""><label for="files">Label</label></div>'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderSingleRadio()
    {
        $element = $this->prepareElement('Radio', ['single-radio' => true]);

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        // no wrapping at all
        $this->expectOutputRegex('/^$/');
    }
}
