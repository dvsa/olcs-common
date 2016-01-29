<?php

namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\OperatingCentreVariationRecordAction;

/**
 * OperatingCentreVariationRecordActionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OperatingCentreVariationRecordActionTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    public function setUp()
    {
        $this->table = m::mock();

        $this->sut = new OperatingCentreVariationRecordAction($this->table);
    }

    public function testRenderNoS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1];
        $column = ['action' => 'FOO'];

        $this->assertNotContains('(Schedule 4/1)', $this->sut->render($data, $column));
    }

    public function testRenderWithS4()
    {
        $this->table->shouldReceive('getFieldset')->with()->once()->andReturn(null);

        $data = ['id' => 1, 's4' => 'FOO'];
        $column = ['action' => 'FOO'];

        $this->assertContains('(Schedule 4/1)', $this->sut->render($data, $column));
    }
}
