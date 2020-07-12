<?php

namespace CommonTest\Service\Table\Type;

use Common\Service\Table\Type\Action;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Type\Action
 */
class ActionTest extends MockeryTestCase
{
    const ID = 9999;

    /** @var Action */
    protected $sut;
    /** @var  m\MockInterface */
    protected $table;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->table = m::mock();

        $this->sut = new Action($this->table);
    }

    /**
     * @dataProvider dpTestRender
     */
    public function testRender($isFieldset, $data, $column, $content, $expect, $internalEdit)
    {
        $mockAuthService = m::mock()
            ->shouldReceive('isGranted')
            ->with('internal-user')
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with('internal-edit')
            ->andReturn($internalEdit)
            ->getMock();

        $this->table
            ->shouldReceive('getAuthService')
            ->andReturn($mockAuthService)
            ->once()
            ->shouldReceive('getFieldset')
            ->once()
            ->andReturn($isFieldset ? 'unit_Fieldset' : null)
            //
            ->shouldReceive('replaceContent')
            ->andReturn('unit_ValueFormat');

        $data['id'] = self::ID;

        static::assertEquals(
            $expect,
            $this->sut->render($data, $column, $content)
        );
    }

    public function dpTestRender()
    {
        return [
            [
                'isFieldSet' => true,
                'data' => [],
                'column' => [
                    'action' => 'unit_Action',
                    'class' => 'unit_Class',
                    'action-attributes' => [
                        'attrA',
                        'attrB',
                    ],
                ],
                'content' => 'unit_Content',
                'expect' =>
                    '<input type="submit" class="unit_Class"' .
                    ' name="unit_Fieldset[action][unit_Action][' . self::ID . ']"' .
                    ' value="unit_Content"' .
                    ' attrA attrB />',
                true,
            ],
            [
                'isFieldSet' => false,
                'data' => [],
                'column' => [
                    'action' => 'unit_Action',
                    'text' => 'unit_Text',
                ],
                'content' => null,
                'expect' =>
                    '<input type="submit" class=""' .
                    ' name="action[unit_Action][' . self::ID . ']"' .
                    ' value="unit_Text"  />',
                true,
            ],
            [
                'isFieldSet' => false,
                'data' => [
                    'field' => 'unit_FldVal',
                ],
                'column' => [
                    'action' => 'unit_Action',
                    'name' => 'field',
                ],
                'content' => null,
                'expect' =>
                    '<input type="submit" class=""' .
                    ' name="action[unit_Action][' . self::ID . ']"' .
                    ' value="unit_FldVal"  />',
                true,
            ],
            [
                'isFieldSet' => false,
                'data' => [
                    'field' => 'unit_FldVal',
                ],
                'column' => [
                    'action' => 'unit_Action',
                    'value_format' => 'unit_ValueFormat',
                ],
                'content' => null,
                'expect' =>
                    '<input type="submit" class=""' .
                    ' name="action[unit_Action][' . self::ID . ']"' .
                    ' value="unit_ValueFormat"  />',
                true,
            ],
            [
                'isFieldSet' => false,
                'data' => [
                    'field' => 'unit_FldVal',
                ],
                'column' => [
                    'action' => 'unit_Action',
                    'value_format' => 'unit_ValueFormat',
                    'keepForReadOnly' => true,
                ],
                'content' => null,
                'expect' => 'unit_ValueFormat',
                false,
            ],
        ];
    }
}
