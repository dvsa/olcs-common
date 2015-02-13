<?php

/**
 * Checkbox Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\Checkbox;

/**
 * Checkbox Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CheckboxTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->table = m::mock();
        $this->sut = new Checkbox($this->table);
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
            '<input type="checkbox" name="table[id][]" value="7" disabled="disabled" />',
            $response
        );
    }

    /**
     * Test render
     * 
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
            '<input type="checkbox" name="table[id][]" value="7"  />',
            $response
        );
    }
}
