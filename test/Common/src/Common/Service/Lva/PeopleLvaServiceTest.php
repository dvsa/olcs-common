<?php

/**
 * People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Printing;

use Common\Service\Helper\FormHelperService;
use Common\Service\Lva\PeopleLvaService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Form\Element;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\Form;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Text\Table\Table;

/**
 * People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PeopleLvaServiceTest extends MockeryTestCase
{
    /** @var ServiceLocatorInterface|m\Mock */
    private $sm;

    /** @var PeopleLvaService */
    private $sut;

    public function setUp(): void
    {
        $this->sm = m::mock(ServiceLocatorInterface::class);
        $this->sut = new PeopleLvaService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testLockPersonForm()
    {
        $mockTitleElement = m::Mock(Element::class);

        $fieldset = m::mock(FieldsetInterface::class);
        $fieldset->shouldReceive('has')
            ->with('title')
            ->andReturn(true);
        $fieldset->shouldReceive('has')
            ->andReturn(false);
        $fieldset->shouldReceive('get')
            ->with('title')
            ->andReturn($mockTitleElement);

        /** @var Form|m\Mock $form */
        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);
        $form->shouldReceive('setAttribute')
            ->with('locked', true)
            ->once();

        $formHelperService = m::mock(FormHelperService::class);
        $formHelperService->shouldReceive('lockElement')
            ->with($mockTitleElement, 'people.org_t_rc.title.locked')
            ->once();
        $formHelperService->shouldReceive('disableElement')
            ->with($form, 'data->title')
            ->once();
        $formHelperService->shouldReceive('remove')
            ->with($form, 'form-actions->submit')
            ->once();

        $this->setService('Helper\Form', $formHelperService);

        $this->sut->lockPersonForm($form, 'org_t_rc');
    }

    public function testLockPartnershipForm()
    {
        /** @var Form|m\Mock $form */
        $form = m::mock(Form::class);

        $table = m::mock(Table::class);
        $table->shouldReceive('removeActions')
            ->once();
        $table->shouldReceive('removeColumn')
            ->with('select')
            ->once();

        $this->sut->lockPartnershipForm($form, $table);
    }

    public function testLockOrganisationForm()
    {
        /** @var Form|m\Mock $form */
        $form = m::mock(Form::class);

        $table = m::mock();
        $table->shouldReceive('removeActions')
            ->once();
        $table->shouldReceive('removeColumn')
            ->with('select')->once();
        $table->shouldReceive('removeColumn')
            ->with('actionLinks')
            ->once();

        $this->sut->lockOrganisationForm($form, $table);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
