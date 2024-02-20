<?php

namespace CommonTest\Service\Table\Type;

use Common\Service\Table\Type\VariationRecordAction;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Type\VariationRecordAction
 */
class VariationRecordActionTest extends MockeryTestCase
{
    /** @var  VariationRecordAction */
    protected $sut;
    /** @var  m\MockInterface */
    protected $table;
    /** @var  m\MockInterface */
    private $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(\Laminas\I18n\Translator\TranslatorInterface::class);

        $mockSm = m::mock(ContainerInterface::class);
        $mockSm->shouldReceive('get')->once()->with('translator')->andReturn($this->mockTranslator);

        $mockAuthService = m::mock()
            ->shouldReceive('isGranted')
            ->with('internal-user')
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with('internal-edit')
            ->andReturn(true)
            ->getMock();

        $this->table = m::mock(\Common\Service\Table\TableBuilder::class)
            ->shouldReceive('getServiceLocator')
            ->once()
            ->andReturn($mockSm)
            ->shouldReceive('getAuthService')
            ->andReturn($mockAuthService)
            ->once()
            ->getMock();

        $this->sut = new VariationRecordAction($this->table);
    }

    /**
     * @dataProvider provider
     */
    public function testRender($action, $prefix, $expected)
    {
        if ($prefix !== null) {
            $this->mockTranslator
                ->shouldReceive('translate')
                ->once()
                ->with('common.table.status.' . $prefix)
                ->andReturn('TRSLTD_STATUS');
        }

        $this->table->shouldReceive('getFieldset')
            ->andReturn('table');

        $data = [
            'id' => 7,
            'link' => 'link-text',
            'action' => $action,
        ];
        $column = [
            'action' => 'foo',
            'name' => 'link',
        ];

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            $expected,
            $response
        );
    }

    public function provider()
    {
        return [
            [
                'action' => 'A',
                'expectPrefix' => 'new',
                'expect' => '(TRSLTD_STATUS) <button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][foo][7]" ' .
                    '>link-text</button>',
            ],
            [
                'action' => 'U',
                'expectPrefix' => 'updated',
                'expect' => '(TRSLTD_STATUS) <button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][foo][7]" '.
                    '>link-text</button>',
            ],
            [
                'action' => 'C',
                'expectPrefix' => 'current',
                'expect' => '(TRSLTD_STATUS) <button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][foo][7]" ' .
                    'disabled="disabled">link-text</button>',
            ],
            [
                'action' => 'D',
                'expectPrefix' => 'removed',
                'expect' => '(TRSLTD_STATUS) <button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][foo][7]" ' .
                    'disabled="disabled">link-text</button>',
            ],
            [
                'action' => 'ABC',
                'expectPrefix' => null,
                'expect' => '<button data-prevent-double-click="true" data-module="govuk-button" role="link" type="submit" class="action-button-link " name="table[action][foo][7]" >link-text</button>',
            ],
        ];
    }
}
