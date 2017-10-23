<?php

/**
 * Selector Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\Selector;

/**
 * Selector Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SelectorTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    public function setUp()
    {
        $this->table = m::mock();

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
            'data-attributes' => array(
                'action'
            )
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
            'data-attributes' => array(
                'action'
            )
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
            'disabled-callback' => function ($row) {
                return $row['isExpiredForLicence'];
            }
        ];

        $this->table
            ->shouldReceive('getFieldset')
            ->andReturn($fieldset)
            ->once();

        $this->assertEquals($expected, $this->sut->render($row, $column));
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
