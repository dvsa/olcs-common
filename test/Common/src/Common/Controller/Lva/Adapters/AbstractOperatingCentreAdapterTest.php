<?php

/**
 * Abstract Operating Centre Adapter Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Abstract Operating Centre Adapter Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractOperatingCentreAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    protected function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->sut = m::mock('\Common\Controller\Lva\Adapters\AbstractOperatingCentreAdapter')
            ->makePartial();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testDisableValidation()
    {
        $inputFilter = m::mock();

        $form = m::mock('\Zend\Form\Form')
            ->shouldReceive('getInputFilter')
            ->andReturn($inputFilter)
            ->getMock();

        $this->sm->setService(
            'Helper\Form',
            m::mock()
            ->shouldReceive('disableValidation')
            ->with($inputFilter)
            ->getMock()
        );

        $this->sut->disableValidation($form);
    }

    public function testAlterFormData()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals($data, $this->sut->alterFormData(2, $data));
    }
}
