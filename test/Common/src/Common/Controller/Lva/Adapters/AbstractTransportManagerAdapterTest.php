<?php

namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Abstract Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class AbstractTransportManagerAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    protected function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->sut = m::mock('\Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter')
            ->makePartial();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetTable()
    {
        $mockTable = m::mock('StdClass');
        $this->sm->shouldReceive('get->prepareTable')->once()->with('template')->andReturn($mockTable);

        $this->assertEquals($mockTable, $this->sut->getTable('template'));
    }

    public function testMustHaveAtLeastOneTm()
    {
        $this->assertFalse($this->sut->mustHaveAtLeastOneTm(99));
    }

    public function testAddMessages()
    {
        // no assertion as its a no op
        $this->sut->addMessages(99);
    }
}
