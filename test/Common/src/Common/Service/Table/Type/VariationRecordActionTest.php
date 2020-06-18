<?php

namespace CommonTest\Service\Table\Type;

use Common\Service\Table\Type\VariationRecordAction;
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
        $this->mockTranslator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);

        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
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
                'expect' => '(TRSLTD_STATUS) <input type="submit" class="" name="table[action][foo][7]" ' .
                    'value="link-text"  />',
            ],
            [
                'action' => 'U',
                'expectPrefix' => 'updated',
                'expect' => '(TRSLTD_STATUS) <input type="submit" class="" name="table[action][foo][7]" '.
                    'value="link-text"  />',
            ],
            [
                'action' => 'C',
                'expectPrefix' => 'current',
                'expect' => '(TRSLTD_STATUS) <input type="submit" class="" name="table[action][foo][7]" ' .
                    'value="link-text" disabled="disabled" />',
            ],
            [
                'action' => 'D',
                'expectPrefix' => 'removed',
                'expect' => '(TRSLTD_STATUS) <input type="submit" class="" name="table[action][foo][7]" ' .
                    'value="link-text" disabled="disabled" />',
            ],
            [
                'action' => 'ABC',
                'expectPrefix' => null,
                'expect' => '<input type="submit" class="" name="table[action][foo][7]" value="link-text"  />',
            ],
        ];
    }
}
