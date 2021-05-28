<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Types\AttachFilesButton;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use Laminas\View\HelperPluginManager;
use Laminas\Form\View\Helper as LaminasHelper;
use Common\Form\View\Helper as CommonHelper;
use Common\Form\View\Helper\FormRow;
use Common\Test\MockeryTestCase;
use Mockery\MockInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\I18n\View\Helper\Translate;
use Laminas\I18n\Translator\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use Laminas\Form\Element;

/**
 * @covers \Common\Form\View\Helper\FormRow
 * @covers \Common\Form\View\Helper\Extended\FormRow
 */
class FormRowTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';
    protected const AN_ELEMENT_NAME = 'AN ELEMENT NAME';
    protected const AN_EMPTY_FIELD = '<div class="field "></div>';
    protected const AN_EMPTY_STRING = '';

    /**
     * @var FormRow|null
     */
    protected $sut;

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_Classic()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement();
        $element->setMessages(['Message']);
        $element->setLabelOption('always_wrap', true);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="validation-wrapper"><div class="field "><p class="error__text">Message<\/p><label>(.*)<\/label>(.*)<\/div><\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ClassicNoLabel()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Text', ['label' => null]);
        $element->setMessages(['Message']);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="validation-wrapper"><div class="field "><p class="error__text">Message<\/p>(.*)<\/div><\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ClassicWithId()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement();
        $element->setAttribute('id', 'test');

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="field "><label(.*)>(.*)<\/label>(.*)<\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ClassicWithPartial()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement();

        // Execute
        $result = $this->sut->__invoke($element, null, null, 'partial');

        // Assert
        $this->assertRegExp('/^<div class="field "><\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersActionButton()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Common\Form\Elements\InputFilters\ActionButton');

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersNoRender()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Common\Form\Elements\InputFilters\NoRender');

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersTable()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Common\Form\Elements\Types\Table');
        $mockTable = $this->getMockBuilder('\Common\Service\Table\TableBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();
        $mockTable->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<table></table>'));
        $element->setTable($mockTable);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="field "><table><\/table><\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersSingleCheckbox()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Common\Form\Elements\InputFilters\SingleCheckbox');
        $element->setLabelOption('always_wrap', true);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="field "><label>(.*)<\/label>(.*)<\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersCheckbox()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Common\Form\Elements\InputFilters\Checkbox',
            [
                'label_options' => [
                    'label_position' => 'append',
                ],
            ]
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="field "><label for="test">(.*)<\/label>(.*)<\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendesrRadioNoAttribute()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Radio');

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<fieldset><legend>(.*)<\/legend><\/fieldset>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersRadioLegendAttribute()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Radio',
            [
                "legend-attributes" => [
                    'class' => 'A_CLASS',
                ],
            ]
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<fieldset><legend class="A_CLASS">(.*)<\/legend><\/fieldset>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersRadioWithDataGroupAttribute()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Radio',
            [
                "fieldset-data-group" => 'data-group',
            ]
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<fieldset data-group="data-group"><legend>(.*)<\/legend><\/fieldset>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersRadioWithInlineAttribute()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Radio',
            [
                "fieldset-attributes" => [
                    "class"      => "inline",
                    "data-group" => "data-group",
                ],
            ]
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<fieldset class="inline" data-group="data-group"><legend>(.*)<\/legend><\/fieldset>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     * @group formRow
     */
    public function __invoke_RendersCsrfElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
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

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<label for="security">Label</label>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     * @group formRow
     */
    public function __invoke_RendersVisuallyHiddenElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Text',
            [
                'name' => 'text',
            ],
            ['class' => 'visually-hidden']
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^<div class="field visually-hidden">(.*)<\/div>$/', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     * @group formRow
     */
    public function __invoke_RendersHiddenElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Hidden',
            [
                'name' => 'hidden',
            ],
            ['class' => 'visually-hidden']
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<label for="test">Label</label>', $result);
    }

    public function renderRadioProvider(): array
    {
        return [
            [null],
            [["class" => ""]],
        ];
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersWithRenderAsFieldset()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement();
        $element->setOption('render_as_fieldset', true);

        // Execute
        $markup = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<fieldset class="fieldset--primary"><legend>Label</legend><p class="hint">Hint</p></fieldset>', $markup);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersReadonlyElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'Common\Form\Elements\Types\Readonly',
            [
                'name'  => 'readonly',
                'label' => 'Foo',
            ],
            []
        );
        $element->setValue('Bar');

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<div class="field read-only "><p>Foo<br><b>Bar</b></p></div>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersDateSelectElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'DateSelect',
            [
                'name'         => 'date',
                'label'        => 'Foo',
                'label-suffix' => 'unit_LabelSfx',
            ],
            []
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<div class="field "><fieldset class="date"><legend>Foo unit_LabelSfx</legend><p class="hint">Hint</p></fieldset></div>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersDateSelectWithFieldsetClass()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'DateSelect',
            [
                'name'          => 'date',
                'label'         => 'Foo',
                'fieldsetClass' => 'user',
                'hint'          => null,
            ],
            []
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<div class="field "><fieldset class="user"><legend>Foo</legend></fieldset></div>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersDateTimeSelectElement()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement(
            'DateTimeSelect',
            [
                'name'  => 'date',
                'label' => 'Foo',
            ],
            []
        );

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<div class="field "><fieldset class="date"><legend>Foo</legend><p class="hint">Hint</p></fieldset></div>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersAttachFilesButtonElement()
    {
        // Setup
        $this->setUpSut();
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

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals('<div class=""><label for="files">Label</label></div>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RendersSingleRadio()
    {
        // Setup
        $this->setUpSut();
        $element = $this->setUpElement('Radio', ['single-radio' => true]);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertRegExp('/^$/', $result);
    }

    /**
     * @return array
     */
    public function allowWrapValuesThatCauseMarkupToBeWrappedDataProvider(): array
    {
        return [
            'an empty string' => [''],
            'a string true' => ['true'],
            'a string false' => ['false'],
            'a zero integer' => [0],
            'a zero float' => [0.0],
            'a integer string with the value one' => ['1'],
            'a integer string with the value zero' => ['0'],
            'an empty array' => [[]],
            'an empty object' => [(object) []],
            'null' => [null],
            'true' => [true],
        ];
    }

    /**
     * @test
     * @dataProvider allowWrapValuesThatCauseMarkupToBeWrappedDataProvider
     * @depends __invoke_IsCallable
     */
    public function __invoke_WrapsMarkupInAField($allowWrapAttributeValue)
    {
        // Setup
        $this->setUpSut();
        $element = new Element(static::AN_ELEMENT_NAME);
        $element->setAttribute('allowWrap', $allowWrapAttributeValue);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals(static::AN_EMPTY_FIELD, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_DoesNotWrapMarkupInAField_IfAllowWrapAttributeIsFalse()
    {
        // Setup
        $this->setUpSut();
        $element = new Element(static::AN_ELEMENT_NAME);
        $element->setAttribute('allowWrap', false);

        // Execute
        $result = $this->sut->__invoke($element);

        // Assert
        $this->assertEquals(static::AN_EMPTY_STRING, $result);
    }

    /**
     * Prepare element for test
     *
     * @param string $type    Element type
     * @param array  $options Options for element
     * @return \Laminas\Form\Element
     */
    private function setUpElement($type = 'Text', $options = [], $attributes = ['class' => 'class'])
    {
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

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut()
    {
        $this->sut = new CommonHelper\FormRow();
        $this->sut->setView($this->phpRenderer());
        $this->sut->setTranslator($this->translator());
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return CommonHelper\FormElementErrors
     */
    protected function setUpFormElementErrors(ServiceLocatorInterface $serviceLocator): CommonHelper\FormElementErrors
    {
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        return (new CommonHelper\FormElementErrorsFactory())->createService($pluginManager);
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
        $serviceManager->setService(static::VALIDATOR_MANAGER, $this->setUpValidatorPluginManager());
        $this->phpRenderer();
    }

    /**
     * @return MockInterface|PhpRenderer
     */
    protected function phpRenderer(): MockObject
    {
        if (! $this->serviceManager->has(PhpRenderer::class)) {
            $instance = $this->createPartialMock(PhpRenderer::class, ['render']);
            $this->serviceManager->setService(PhpRenderer::class, $instance);
            $instance->setHelperPluginManager($this->viewHelperPluginManager());
        }
        $instance = $this->serviceManager->get(PhpRenderer::class);
        assert($instance instanceof MockObject);
        return $instance;
    }

    /**
     * @return HelperPluginManager
     */
    protected function viewHelperPluginManager(): HelperPluginManager
    {
        if (! $this->serviceManager->has(HelperPluginManager::class)) {
            $instance = new HelperPluginManager();
            $translateHelper = new Translate();
            $translateHelper->setTranslator($this->translator());
            $instance->setService('translate', $translateHelper);
            $instance->setService('form_label', new LaminasHelper\FormLabel());
            $instance->setService('form_element', new CommonHelper\FormElement());
            $instance->setService('form_text', new LaminasHelper\FormText());

            $formElementErrors = $this->setUpFormElementErrors($this->serviceManager);
            $formElementErrors->setView($this->phpRenderer());
            $instance->setService('form_element_errors', $formElementErrors);

            $this->serviceManager->setService(HelperPluginManager::class, $instance);
        }
        $instance = $this->serviceManager->get(HelperPluginManager::class);
        assert($instance instanceof HelperPluginManager);
        return $instance;
    }

    /**
     * @return Translator
     */
    protected function translator(): Translator
    {
        if (! $this->serviceManager->has(TranslatorInterface::class)) {
            $instance = new Translator();
            $this->serviceManager->setService(TranslatorInterface::class, $instance);
        }
        $instance = $this->serviceManager->get(TranslatorInterface::class);
        assert($instance instanceof Translator);
        return $instance;
    }

    /**
     * @return ValidatorPluginManager
     */
    protected function setUpValidatorPluginManager(): ValidatorPluginManager
    {
        return new ValidatorPluginManager();
    }
}
