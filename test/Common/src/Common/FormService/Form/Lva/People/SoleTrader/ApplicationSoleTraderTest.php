<?php

namespace CommonTest\FormService\Form\Lva\People\SoleTrader;

use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Lva\PeopleLvaService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\People\SoleTrader\ApplicationSoleTrader as Sut;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

/**
 * Application Sole Trader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSoleTraderTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $fsm;

    protected $sm;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->authService = m::mock(AuthorizationService::class);
        $this->peopleLvaService = m::mock(PeopleLvaService::class);
        $this->mockVariationService = m::mock(FormServiceInterface::class);
        $this->fsl = m::mock(FormServiceManager::class)->makePartial();

        $this->mockApplicationService = m::mock(FormServiceInterface::class);

        $this->fsl->shouldReceive('get')
            ->with('lva-application')
            ->andReturn($this->mockApplicationService);

        $this->sut = new Sut($this->formHelper, $this->authService, $this->peopleLvaService);
    }

    /**
     * @dataProvider noDisqualifyProvider
     */
    public function testGetFormNoDisqualify($params)
    {
        $params['canModify'] = true;

        $formActions = m::mock();
        $formActions->shouldReceive('has')->with('disqualify')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('disqualify');

        $form = m::mock();

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $this->sut->getForm($params);
    }

    public function testGetForm()
    {
        $params = [
            'location' => 'internal',
            'personId' => 123,
            'isDisqualified' => false,
            'canModify' => true,
            'disqualifyUrl' => 'foo'
        ];

        $formActions = m::mock();
        $formActions->shouldReceive('get->setValue')
            ->once()
            ->with('foo');

        $form = m::mock(Form::class);

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $this->sut->getForm($params);
    }

    public function testGetFormCantModify()
    {
        $params = [
            'location' => 'internal',
            'personId' => 123,
            'isDisqualified' => false,
            'canModify' => false,
            'disqualifyUrl' => 'foo',
            'orgType' => 'bar'
        ];

        $formActions = m::mock();
        $formActions->shouldReceive('get->setValue')
            ->once()
            ->with('foo');

        $form = m::mock(Form::class);

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $this->peopleLvaService->shouldReceive('lockPersonForm')
            ->once()
            ->with($form, 'bar');

        $this->sut->getForm($params);
    }

    public function noDisqualifyProvider()
    {
        return [
            [
                ['location' => 'external']
            ],
            [
                [
                    'location' => 'internal',
                    'personId' => null
                ]
            ],
            [
                [
                    'location' => 'internal',
                    'personId' => 123,
                    'isDisqualified' => true
                ]
            ],
        ];
    }
}
