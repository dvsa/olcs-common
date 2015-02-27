<?php

/**
 * People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Printing;

use CommonTest\Bootstrap;
use Common\Service\Lva\PeopleLvaService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PeopleLvaServiceTest extends MockeryTestCase
{
    private $sm;
    private $sut;

    public function setup()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $this->sut = new PeopleLvaService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testLockPersonForm()
    {
        $fieldset = m::mock()
            ->shouldReceive('has')
            ->with('title')
            ->andReturn(true)
            ->shouldReceive('has')
            ->andReturn(false)
            ->shouldReceive('get')
            ->with('title')
            ->andReturn('title')
            ->getMock();

        $form = m::mock('Zend\Form\Form')
            ->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset)
            ->getMock();

        $this->setService(
            'Helper\Form',
            m::mock()
            ->shouldReceive('lockElement')
            ->with('title', 'people.org_t_rc.title.locked')
            ->shouldReceive('disableElement')
            ->with($form, 'data->title')
            ->shouldReceive('remove')
            ->with($form, 'form-actions->submit')
            ->getMock()
        );

        $this->sut->lockPersonForm($form, 'org_t_rc');
    }

    public function testLockPartnershipForm()
    {
        $form = m::mock('Zend\Form\Form');

        $table = m::mock()
            ->shouldReceive('removeActions')
            ->shouldReceive('removeColumn')
            ->with('select')
            ->getMock();

        $this->sut->lockPartnershipForm($form, $table);
    }

    public function testLockOrganisationForm()
    {
        $form = m::mock('Zend\Form\Form');

        $table = m::mock()
            ->shouldReceive('removeActions')
            ->shouldReceive('removeColumn')
            ->with('select')
            ->getMock();

        $this->sut->lockOrganisationForm($form, $table);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
