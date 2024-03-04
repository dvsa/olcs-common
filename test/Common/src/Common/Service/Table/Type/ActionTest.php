<?php

namespace CommonTest\Service\Table\Type;

use Common\Service\Table\Type\Action;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Type\Action
 */
class ActionTest extends MockeryTestCase
{
    public const ID = 9999;

    /** @var Action */
    protected $sut;
    /** @var  m\MockInterface */
    protected $table;

    public function setUp(): void
    {
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
                    '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit"' .
                    ' class="action-button-link unit_Class" name="unit_Fieldset[action][unit_Action][' . self::ID . ']"' .
                    ' attrA attrB>unit_Content</button>',
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
                    '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit"' .
                    ' class="action-button-link " name="action[unit_Action][' . self::ID . ']"' .
                    ' >unit_Text</button>',
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
                    '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit"' .
                    ' class="action-button-link " name="action[unit_Action][' . self::ID . ']"' .
                    ' >unit_FldVal</button>',
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
                    '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit"' .
                    ' class="action-button-link " name="action[unit_Action][' . self::ID . ']"' .
                    ' >unit_ValueFormat</button>',
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
