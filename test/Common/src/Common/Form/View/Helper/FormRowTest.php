<?php

/**
 * FormRow Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\Form\View\Helper as ZendHelper;
use Common\Form\View\Helper as CommonHelper;

/**
 * FormRow Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormRowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @param array $options
     * @return \Zend\Form\Element
     */
    private function prepareElement($type = 'Text', $options = array(), $attributes = array('class' => 'class'))
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
            '/^<div class="validation-wrapper"><div class="field ">'.
            '<ul><li>(.*)<\/li><\/ul><label>(.*)<\/label>(.*)<\/div><\/div>$/'
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
            '/^<div class="validation-wrapper"><div class="field ">'.
            '<ul><li>(.*)<\/li><\/ul>(.*)<\/div><\/div>$/'
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

        $this->expectOutputRegex('/^<div class="field "><label>(.*)<\/label>(.*)<\/div>$/');
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
                    'label_position' => 'append'
                ]
            ]
        );

        $viewHelper = $this->prepareHelper();
        echo $viewHelper($element);

        $this->expectOutputRegex('/^<div class="field "><label for="test">(.*)<\/label>(.*)<\/div>$/');
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
    public function testRenderRadioWithDataGroupAttribute()
    {
        $element = $this->prepareElement(
            'Radio',
            [
                "fieldset-data-group" => 'data-group'
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
                    "class" => "inline",
                    "data-group" => "data-group"
                ]
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
                        'notSame' => 'csrf-message'
                    ],
                    'timeout' => 600
                ],
                'name' => 'security'
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
                'name' => 'text'
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
                'name' => 'hidden'
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
            [["class" => ""]]
        ];
    }

    private function prepareHelper()
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $translateHelper = new \Zend\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);
        $helpers->setService('form_label', new ZendHelper\FormLabel());
        $helpers->setService('form_element', new CommonHelper\FormElement());
        $helpers->setService('form_text', new ZendHelper\FormText());

        $view = $this->getMock('Zend\View\Renderer\PhpRenderer', array('render'));
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
}
