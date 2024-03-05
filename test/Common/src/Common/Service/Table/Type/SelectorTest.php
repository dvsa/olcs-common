<?php

/**
 * Selector Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Laminas\I18n\Translator\Translator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\Selector;

class SelectorTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    public function setUp(): void
    {
        $this->table = m::mock();
        $this->table->shouldIgnoreMissing();

        $this->sut = new Selector($this->table);
    }

    /**
     * @group checkboxTest
     */
    public function testRender()
    {
        $fieldset = 'table';
        $data = [
            'id' => 7
        ];
        $column = [];

        $this->table->shouldReceive('getFieldset')
            ->andReturn($fieldset);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="table[id]" value="7" id="table[id][7]" />',
            $response
        );
    }

    /**
     * Test render with disabled attribute
     *
     * @group checkboxTest
     */
    public function testRenderWithDisabledAttribute()
    {
        $fieldset = 'table';
        $data = [
            'id' => 7
        ];
        $column = [
            'disableIfRowIsDisabled' => true
        ];

        $this->table
            ->shouldReceive('getFieldset')
            ->andReturn($fieldset)
            ->shouldReceive('isRowDisabled')
            ->with($data)
            ->andReturn(true);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="table[id]" value="7" disabled="disabled" id="table[id][7]" />',
            $response
        );
    }

    /**
     * @group checkboxTest
     */
    public function testRenderWithoutFieldet()
    {
        $fieldset = null;
        $data = [
            'id' => 7
        ];
        $column = [];

        $this->table->shouldReceive('getFieldset')
            ->andReturn($fieldset);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="id" value="7" id="[id][7]" />',
            $response
        );
    }

    /**
     * @group checkboxTest
     */
    public function testRenderWithDataAttributes()
    {
        $fieldset = null;
        $data = [
            'id' => 7,
            'action' => 'blap'
        ];
        $column = [
            'data-attributes' => [
                'action'
            ]
        ];

        $this->table->shouldReceive('getFieldset')
            ->andReturn($fieldset);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="id" value="7" data-action="blap" id="[id][7]" />',
            $response
        );
    }

    /**
     * Test render with data attribute when column is an array
     *
     * @group checkboxTest
     */
    public function testRenderWithDataAttributesArray()
    {
        $fieldset = null;
        $data = [
            'id' => 7,
            'action' => ['id' => 'blap']
        ];
        $column = [
            'data-attributes' => [
                'action'
            ]
        ];

        $this->table->shouldReceive('getFieldset')
            ->andReturn($fieldset);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="id" value="7" data-action="blap" id="[id][7]" />',
            $response
        );
    }

    /**
     * @group checkboxTest
     */
    public function testRenderWithDataIdxSet()
    {
        $fieldset = null;
        $data = [
            'fooBarId' => 7,
        ];
        $column = [
            'idIndex' => 'fooBarId'
        ];

        $this->table->shouldReceive('getFieldset')
            ->andReturn($fieldset);

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            '<input type="radio" name="id" value="7" id="[id][7]" />',
            $response
        );
    }

    /**
     * Test render with disabled callback
     *
     * @group checkboxTest
     * @dataProvider disabledCallbackProvider
     */
    public function testRenderWithDisabledCallback($row, $expected)
    {
        $fieldset = 'table';
        $column = [
            'disabled-callback' => fn($row) => $row['isExpiredForLicence']
        ];

        $this->table
            ->shouldReceive('getFieldset')
            ->andReturn($fieldset)
            ->once();

        $this->assertEquals($expected, $this->sut->render($row, $column));
    }

    /**
     * Test render with a single aria attribute defined as a string literal.
     *
     * @test
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_LiteralStringDefinition_Single()
    {
        $column = [
            'aria-attributes' => [
                'label' => 'Some Aria Attribute'
            ]
        ];

        $this->assertStringContainsString(
            ' aria-label="Some Aria Attribute" ',
            $this->sut->render(['id' => 7], $column)
        );
    }

    /**
     * Test render with a multiple aria attribute defined as string literals.
     *
     * @test
     * @depends render_WithAriaAttribute_LiteralStringDefinition_Single
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_LiteralStringDefinition_Multiple()
    {
        $column = [
            'aria-attributes' => [
                'label' => 'Some Aria Attribute',
                'checked' => 'false',
                'test' => 'testing'
            ]
        ];

        $renderedResult = $this->sut->render(['id' => 7], $column);
        $this->assertStringContainsString(' aria-label="Some Aria Attribute" ', $renderedResult);
        $this->assertStringContainsString(' aria-checked="false" ', $renderedResult);
        $this->assertStringContainsString(' aria-test="testing" ', $renderedResult);
    }

    /**
     * Test render with aria attribute value being a callback.
     *
     * @test
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_AsCallback()
    {
        $column = [
            'aria-attributes' => [
                'label' => fn() => 'Test translated string'
            ]
        ];

        $this->assertStringContainsString(
            ' aria-label="Test translated string" ',
            $this->sut->render(['id' => 7], $column)
        );
    }

    /**
     * Test render with aria attribute being a callback, translator is passed to callable.
     *
     * @test
     * @depends render_WithAriaAttribute_AsCallback
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_AsCallback_TranslatorIsPassedToCallable()
    {
        $translatorMock = m::mock(Translator::class);
        $this->table
            ->shouldReceive('getTranslator')
            ->andReturn($translatorMock);

        $column = [
            'aria-attributes' => [
                'label' => function ($data, $translator) use ($translatorMock) {
                    $this->assertSame($translatorMock, $translator);
                }
            ]
        ];

        $this->sut->render(['id' => 7], $column);
    }

    /**
     * Test render with aria attribute being a callback, data is passed to callable.
     *
     * @test
     * @depends render_WithAriaAttribute_AsCallback
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_AsCallback_DataIsPassedToCallable()
    {
        $expectedData = ['id' => 7];

        $column = [
            'aria-attributes' => [
                'label' => function ($data) use ($expectedData) {
                    $this->assertSame($expectedData, $data);
                }
            ]
        ];

        $this->sut->render($expectedData, $column);
    }

    /**
     * Test render with aria attribute contain HTML is escaped
     *
     * @test
     * @group tableSelectorAriaSupport
     */
    public function render_WithAriaAttribute_HtmlIsEscaped()
    {
        $column = [
            'aria-attributes' => [
                'label' => 'Some <html>'
            ]
        ];

        $this->assertStringNotContainsString(
            '<html>',
            $this->sut->render(['id' => 7], $column)
        );
    }

    public function disabledCallbackProvider()
    {
        return [
            [
                ['isExpiredForLicence' => 1, 'id' => 7],
                '<input type="radio" name="table[id]" value="7" disabled="disabled" id="table[id][7]" />'
            ],
            [
                ['isExpiredForLicence' => 0, 'id' => 7],
                '<input type="radio" name="table[id]" value="7" id="table[id][7]" />'
            ]
        ];
    }
}
