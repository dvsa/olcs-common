<?php

namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Continuation\LicenceChecklist;
use Common\Form\Model\Form\Continuation\LicenceChecklist as LicenceChecklistForm;
use Common\Service\Helper\FormHelperService;
use Common\RefData;
use CommonTest\Bootstrap;
use Common\FormService\FormServiceManager;

/**
 * Licence checklist form service test
 */
class LicenceChecklistTest extends MockeryTestCase
{
    /** @var LicenceChecklist */
    protected $sut;
    /** @var  m\MockInterface */
    private $formHelper;
    /** @var  m\MockInterface */
    protected $urlHelper;

    public function setUp()
    {
        $this->formHelper = m::mock(FormHelperService::class);

        $this->urlHelper = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Helper\Url', $this->urlHelper);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut = new LicenceChecklist();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($fsm);
    }

    public function testAlterFormSmallCount()
    {
        $form = m::mock(LicenceChecklistForm::class)
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('peopleCheckbox')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getLabel')
                            ->andReturn('label.')
                            ->once()
                            ->shouldReceive('setLabel')
                            ->with('label.' . RefData::ORG_TYPE_REGISTERED_COMPANY)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('viewPeopleSection')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('get')
                            ->with('viewPeople')
                            ->andReturn(
                                m::mock()
                                    ->shouldReceive('getLabel')
                                    ->andReturn('label.')
                                    ->once()
                                    ->shouldReceive('setLabel')
                                    ->with('label.' . RefData::ORG_TYPE_REGISTERED_COMPANY)
                                    ->once()
                                    ->getMock()
                            )
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->formHelper
            ->shouldReceive('createForm')
            ->with(LicenceChecklistForm::class)
            ->once()
            ->andReturn($form)
            ->shouldReceive('remove')
            ->with($form, 'data->viewPeopleSection')
            ->once()
            ->shouldReceive('remove')
            ->with($form, 'data->viewVehiclesSection')
            ->once()
            ->andReturnSelf();

        $data = [
            'licence' => [
                'organisation' => [
                    'type' => [
                        'id' => RefData::ORG_TYPE_REGISTERED_COMPANY
                    ],
                    'organisationPersons' => ['foo']
                ],
                'licenceVehicles' => [
                    ['vehicle' => 'foo'],
                ]
            ],
            'id' => 1
        ];
        $this->assertEquals($form, $this->sut->getForm($data));
    }

    public function testAlterFormLargeCount()
    {
        $form = m::mock(LicenceChecklistForm::class)
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('peopleCheckbox')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getLabel')
                            ->andReturn('label.')
                            ->once()
                            ->shouldReceive('setLabel')
                            ->with('label.' . RefData::ORG_TYPE_REGISTERED_COMPANY)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('viewPeopleSection')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('get')
                            ->with('viewPeople')
                            ->andReturn(
                                m::mock()
                                    ->shouldReceive('getLabel')
                                    ->andReturn('label.')
                                    ->once()
                                    ->shouldReceive('setLabel')
                                    ->with('label.' . RefData::ORG_TYPE_REGISTERED_COMPANY)
                                    ->once()
                                    ->shouldReceive('setValue')
                                    ->with('url')
                                    ->once()
                                    ->getMock()
                            )
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('viewVehiclesSection')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('get')
                            ->with('viewVehicles')
                            ->andReturn(
                                m::mock()
                                    ->shouldReceive('setValue')
                                    ->with('url1')
                                    ->once()
                                    ->getMock()
                            )
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->twice()
            ->getMock();

        $this->urlHelper->shouldReceive('fromRoute')
            ->with(
                'continuation/checklist/people',
                [
                    'continuationDetailId' => 1,
                ]
            )
            ->andReturn('url')
            ->once()
            ->shouldReceive('fromRoute')
            ->with(
                'continuation/checklist/vehicles',
                [
                    'continuationDetailId' => 1,
                ]
            )
            ->andReturn('url1')
            ->once()
            ->getMock();

        $this->formHelper
            ->shouldReceive('createForm')
            ->with(LicenceChecklistForm::class)
            ->once()
            ->andReturn($form)
            ->shouldReceive('remove')
            ->with($form, 'data->people')
            ->once()
            ->shouldReceive('remove')
            ->with($form, 'data->vehicles')
            ->once()
            ->andReturnSelf();

        $data = [
            'licence' => [
                'organisation' => [
                    'type' => [
                        'id' => RefData::ORG_TYPE_REGISTERED_COMPANY
                    ],
                    'organisationPersons' => array_fill(0, RefData::CONTINUATIONS_DISPLAY_PERSON_COUNT + 1, 'foo')
                ],
                'licenceVehicles' => array_fill(0, RefData::CONTINUATIONS_DISPLAY_VEHICLES_COUNT + 1, 'foo')
            ],
            'id' => 1
        ];
        $this->assertEquals($form, $this->sut->getForm($data));
    }
}
