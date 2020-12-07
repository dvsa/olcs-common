<?php

/**
 * Licence Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceLvaAdapter;

/**
 * Licence Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp(): void
    {
        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Laminas\Mvc\Controller\AbstractController');

        $this->sut = new LicenceLvaAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testAlterForm()
    {
        $mockForm = m::mock('\Laminas\Form\Form');

        $mockForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('saveAndContinue')
                ->getMock()
            );

        $this->assertNull($this->sut->alterForm($mockForm));
    }
}
