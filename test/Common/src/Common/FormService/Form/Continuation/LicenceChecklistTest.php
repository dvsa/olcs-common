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

    public function testAlterForm()
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
                            ->shouldReceive('getOption')
                            ->with('not_checked_message')
                            ->andReturn('message.')
                            ->once()
                            ->shouldReceive('setOption')
                            ->with('not_checked_message', 'message.'  . RefData::ORG_TYPE_REGISTERED_COMPANY)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('licenceChecklistConfirmation')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('get')
                            ->with('noContent')
                            ->andReturn(
                                m::mock()
                                    ->shouldReceive('get')
                                    ->with('backToLicence')
                                    ->andReturn(
                                        m::mock()
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
                            ->with('yesContent')
                            ->andReturn(
                                m::mock()
                                    ->shouldReceive('get')
                                    ->with('submit')
                                    ->andReturn(
                                        m::mock()
                                            ->shouldReceive('setLabel')
                                            ->with('continuations.checklist.confirmation.yes-button-declaration')
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
                    ->getMock()
            )
            ->times(3)
            ->getMock();

        $this->urlHelper->shouldReceive('fromRoute')
            ->with(
                'lva-licence',
                ['licence' => 2]
            )
            ->andReturn('url')
            ->once()
            ->getMock();

        $this->formHelper
            ->shouldReceive('createForm')
            ->with(LicenceChecklistForm::class)
            ->andReturn($form)
            ->once()
            ->shouldReceive('remove')
            ->with($form, 'data->conditionsUndertakingsCheckbox')
            ->once()
            ->getMock();

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
                ],
                'id' => 2,
                'licenceType' => [
                    'id' => RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
                ],
                'goodsOrPsv' => [
                    'id' => RefData::LICENCE_CATEGORY_PSV
                ]
            ],
            'id' => 1,
            'sections' => [
                'typeOfLicence',
                'businessType',
                'businessDetails',
                'addresses',
                'people',
                'operatingCentres',
                'transportManagers',
                'vehiclesPsv',
                'safety',
            ]
        ];
        $this->assertEquals($form, $this->sut->getForm($data));
    }
}
