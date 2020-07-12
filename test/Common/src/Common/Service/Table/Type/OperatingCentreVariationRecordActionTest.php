<?php

namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\OperatingCentreVariationRecordAction;

/**
 * @covers Common\Service\Table\Type\OperatingCentreVariationRecordAction
 */
class OperatingCentreVariationRecordActionTest extends MockeryTestCase
{
    /** @var  OperatingCentreVariationRecordAction */
    protected $sut;
    /** @var  m\MockInterface */
    protected $table;

    public function setUp(): void
    {
        $mockTranslator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);

        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSm->shouldReceive('get')->once()->with('translator')->andReturn($mockTranslator);

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

        $this->sut = new OperatingCentreVariationRecordAction($this->table);
    }

    public function testRenderNoS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1];
        $column = ['action' => 'FOO'];

        $this->assertStringNotContainsString('(Schedule 4/1)', $this->sut->render($data, $column));
    }

    public function testRenderWithS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1, 's4' => 'FOO'];
        $column = ['action' => 'FOO'];

        $this->assertStringContainsString('(Schedule 4/1)', $this->sut->render($data, $column));
    }
}
