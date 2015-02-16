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
            '<input type="radio" name="table[id]" value="7"  />',
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
            '<input type="radio" name="table[id]" value="7" disabled="disabled" />',
            $response
        );
    }

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
            '<input type="radio" name="id" value="7"  />',
            $response
        );
    }

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
            '<input type="radio" name="id" value="7" data-action="blap" />',
            $response
        );
    }
}
